<?php

namespace Application\Service;

use Tlumx\ServiceContainer\ServiceContainer;
use Application\Entity\User;
use Application\Entity\UserInterface;
use Application\Security\Password;

class UsersService
{
    const table_users = 'users';
    
    const table_confirm = 'users_confirm_password';
    
    const date_format = 'Y-m-d H:i:s';


    protected $roles = [
        'admin'     => 'Administrator',
        'manager'   => 'Manager'
    ];       
    
    protected $defaultRole = 'manager';

    protected $container;
    
    /**
     *
     * @var \Tlumx\Db\Db
     */
    protected $db;
    
    protected $tablePrefix;

    public function __construct(ServiceContainer $container)
    {
        $this->container = $container;
    }    
    
    public function getDb()
    {
        if(!$this->db) {
            $this->db = $this->container->get('db');
        }
        return $this->db;
    }
    
    public function getTablePrefix()
    {
        if(!$this->tablePrefix) {
            $config = $this->container->getConfig('db');
            $this->tablePrefix = isset($config['table_prefix']) ? 
                    $config['table_prefix'] : '';
        }
        
        return $this->tablePrefix;
    }
    
    public function getUsersTable()
    {
        return $this->getTablePrefix() . self::table_users;
    }

    public function getUserConfirmTable()
    {
        return $this->getTablePrefix() . self::table_confirm;
    }    
    
    public function getRoles()
    {
        return $this->roles;
    }
    
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }
    
    public function getUserById($id)
    {
        $result = $this->getDb()->findRow(
            "SELECT * FROM ". $this->getUsersTable() ." WHERE id = :id",
            ['id' => $id]
        );
        
        if (!empty($result)) {
            $user = new User();
            $user->setId($result['id']);
            $user->setUsername($result['username']);
            $user->setEmail($result['email']);
            $user->setDisplayName($result['display_name']);
            $user->setPassword($result['password']);
            $user->setRole($result['role']);
            $user->setLastLogin($result['last_login']);
            $user->setCreated($result['created']);
            return $user;
        }
        
        return null;
    }
    
    public function getUserByUsername($username)
    {
        $result = $this->getDb()->findRow(
            "SELECT * FROM ". $this->getUsersTable() ." WHERE username = :username",
            ['username' => $username]
        );
        
        if (!empty($result)) {
            $user = new User();
            $user->setId($result['id']);
            $user->setUsername($result['username']);
            $user->setEmail($result['email']);
            $user->setDisplayName($result['display_name']);
            $user->setPassword($result['password']);
            $user->setRole($result['role']);
            $user->setLastLogin($result['last_login']);
            $user->setCreated($result['created']);
            return $user;
        }
        
        return null;
    }    
    
    public function usernameExists($username)
    {
        $sql = "SELECT COUNT(*) FROM ".$this->getUsersTable()." WHERE username = :username";
        $result = $this->getDb()->findOne($sql, array('username' => $username));
        return $result > 0;
    }    
    
    public function emailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM ".$this->getUsersTable()." WHERE email = :email";
        $result = $this->getDb()->findOne($sql, array('email' => $email));
        return $result > 0;
    }
    
    public function insertUser(UserInterface $user)
    {
        return $this->getDb()->insert($this->getUsersTable(), array(
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'display_name' => $user->getDisplayName(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'last_login' => null,
            'created' => date(self::date_format, $user->getCreated())
        ));        
        
    }
    
    public function updateUser(UserInterface $user)
    {
        return $this->getDb()->update($this->getUsersTable(), array(
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'display_name' => $user->getDisplayName(),
            'role' => $user->getRole(),
            'last_login' => $user->getLastLogin()
        ), ['id' => $user->getId()]);         
        
    }
    
    public function changePassword($userId, $password)
    {
        $password = $this->createPassword($password);
        $this->getDb()->update($this->getUsersTable(), array(
            'password' => $password
        ), ['id' => $userId]);      
        
        return true;        
    }    
    
    public function getUsernames()
    {
        $usernames = [];
        $result = $this->getDb()->findRows("SELECT id, username FROM ".$this->getUsersTable());        
        if($result) {
            foreach ($result as $row) {
                $usernames[$row['id']] = $row['username'];
            }
        }
        return $usernames;
    }
    
    public function deleteUser(UserInterface $user)
    {
        return $this->getDb()->delete($this->getUsersTable(), ['id' => $user->getId()]);      
    }    
    
    public function fetchPassword($email)
    {                        
        $result = $this->getDb()->findRow(
            "SELECT id, username, display_name FROM ". $this->getUsersTable() ." WHERE email = :email",
            ['email' => $email]
        );
        
        if (empty($result)) {
            return false;
        }
        
        $resultExist = $this->getDb()->findRow(
            "SELECT time FROM ". $this->getUserConfirmTable() ." WHERE user_id = :user_id",
            ['user_id' => $result['id']]
        );
        
        if (!empty($result)) {
            $time = time() - strtotime($resultExist['time']);
            if ($time > 3600) {
                $this->getDb()->delete($this->getUserConfirmTable(), ['user_id' => $result['id']]);                
            } else {
                return true;
            }
        } 
        
        $newP = $this->generatePassword();        
        $passwordKey = sha1(uniqid().$result['id'].$newP);
        $res = $this->getDb()->insert($this->getUserConfirmTable(), array(
            'user_id' => $result['id'],
            'password' => $this->createPassword($newP),
            'password_key' => $passwordKey,
            'time' => date("Y-m-d H:i:s")
        ));      
        if(!$res) {
            return false;
        }
        
        if(!$this->sendConfirmationEmail($email, $result['id'], $result['username'], $result['display_name'], $newP, $passwordKey)) {
            return false;
        }
        
        return true;
    }
    
    public function confirmPassword($userId, $key)
    {
        $result = $this->getDb()->findRow(
            "SELECT * FROM ". $this->getUserConfirmTable() ." WHERE user_id = :user_id",
            ['user_id' => $userId]
        );
        
        if (empty($result)) {
            return false;
        }        
        
        $time = time() - strtotime($result['time']);
        if ($time > 3600) {
            return false;
        }
        
        if($key != $result['password_key']) {
            return false;
        }
        
        $this->getDb()->update($this->getUsersTable(), array(
            'password' => $result['password']     
        ), ['id' => $result['user_id']]);
        
        $this->getDb()->delete($this->getUserConfirmTable(), ['user_id' => $result['user_id']]);
        
        return true;
    }
    
    public function sendConfirmationEmail($email, $id, $username, $displayName, $password, $passwordKey)
    {
        $router = $this->container->getRouter('router');        
        $siteUrl = rtrim($this->container->getConfig('site_url'));                
        $view = $this->container->getView();
        $view->displayName = $displayName;
        $router->setBasePath('');
        $view->url = $siteUrl . $router->createPath('account_forgot_password', [], ['action'=>'confirm', 'id'=>$id, 'key'=>$passwordKey]);
        $router->setBasePath($this->container->getConfig('base_path'));
        $view->username = $username;
        $view->password = $password;        
        $templates = $this->container->getConfig('templates');
        if(!isset($templates['mailfetchpassword'])) {
            throw new \RuntimeException('Template "mailfetchpassword" not isset.');
        }
        
        $file = $templates['mailfetchpassword'];
        $body = $view->renderFile($file);
        $config = $this->container->getConfig('email');
        
        $fromName = isset($config['from_name']) ? $config['from_name'] : '';
        $fromEmail = isset($config['from_email']) ? $config['from_email'] : '';    
        
        try {
            $message = \Swift_Message::newInstance('Confirmation password')
                    ->setFrom(array($fromEmail => $fromName))
                    ->setTo(array($email => $displayName))
                    ->setBody($body, 'text/html');

            $transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
                    ->setEncryption($config['encryption'])
                    ->setUsername($config['username'])
                    ->setPassword($config['password']);

            $mailer = \Swift_Mailer::newInstance($transport);            
            $result = $mailer->send($message);
            if($result) {
                return true;
            }
            return false;            
        } catch (\Exception $e) {
            return false;
        }
    }    
    
    public function createPassword($password)
    {
        return Password::create($password);
    }
    
    public function verify($password, $hash)
    {
        return Password::verify($password, $hash);
    }
    
    public function generatePassword($size = 8)
    {
        return Password::generatePassword($size);
    }    
}