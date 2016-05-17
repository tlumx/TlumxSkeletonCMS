<?php

namespace Application\Controller;

use Tlumx\Controller;
use Application\Verification\CreateUserVerification;
use Application\Verification\EditUserVerification;
use Application\Entity\User;

class UsersController extends Controller
{
    protected $usersService;
    
    public function listAction()
    {
        $service = $this->getUsersService();
        
        $this->getView()->users = $service->getUsernames();
        echo $this->render();
    }
    
    public function createAction()
    {
        $service = $this->getUsersService();
        $roles = $service->getRoles();
        $translator = $this->getServiceProvider()->get('translator');
        
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {            
            $v = new CreateUserVerification($this->getServiceProvider()->getRequest());
            if($v->process()) {
                $user = new User();
                $user->setUsername($v->username);
                $user->setDisplayName($v->displayName);
                $user->setEmail($v->email);
                $password = $service->createPassword($v->password);
                $user->setPassword($password);
                $role = array_key_exists($v->role, $roles) ? $v->role : $service->getDefaultRole(); 
                $user->setRole($role);
                $user->setLastLogin(null);
                $user->setCreated(time());
                
                if($service->usernameExists($user->getUsername())) {
                    $this->getView()->showAlert = $translator->translate("This username is already registered");
                } elseif($service->emailExists($user->getEmail())) {
                    $this->getView()->showAlert = $translator->translate("This email is already registered");
                } elseif($service->insertUser($user)) {
                    $this->redirectToRoute('users_list');
                    return;
                }                
                
            } else {
                $this->getView()->showAlert = $translator->translate("This form was completed correctly");
            }
        }
                        
        $this->getView()->roles = $roles;
        echo $this->render();
    }    
    
    public function checkusernameAction()
    {
        $translator = $this->getServiceProvider()->get('translator');
        $service = $this->getUsersService();       
        $params = $this->getServiceProvider()->getRequest()->getQueryParams();
        $userId = $this->getServiceProvider()->getRequest()->getAttribute('user_id', 0);        
        $value = isset($params['value']) ? strval($params['value']) : '';
        $field = isset($params['field']) ? strval($params['field']) : '';
      
        if($field == 'username' && $value != '') {
            if($userId != 0) {
                $user = $service->getUserById($userId);
                if($user === null) {
                    $this->sendJSON([
                        "value" => $value,
                        "valid" => 0,
                        "message" => $translator->translate('Invalid username')            
                    ]); 
                } elseif($user->getUsername() == $value) {
                    $this->sendJSON([
                        "value" => $value,
                        "valid" => 1
                    ]);
                }
            }
            if($service->usernameExists($value)) {
                $this->sendJSON(array(
                    "value" => $value,
                    "valid" => 0,
                    "message" => $translator->translate('This username is already registered')
                ));
            } else {
                $this->sendJSON(array(
                    "value" => $value,
                    "valid" => 1
                ));
            }
        }
        
        $this->sendJSON([
            "value" => $value,
            "valid" => 0,
            "message" => $translator->translate('Invalid username')            
        ]);       
    }    
    
    public function checkuseremailAction()
    {
        $translator = $this->getServiceProvider()->get('translator');
        $service = $this->getUsersService(); 
        $params = $this->getServiceProvider()->getRequest()->getQueryParams();
        $userId = $this->getServiceProvider()->getRequest()->getAttribute('user_id', 0);        
        $value = isset($params['value']) ? strval($params['value']) : '';
        $field = isset($params['field']) ? strval($params['field']) : '';
      
        if($field == 'email' && $value != '') {
            if($userId != 0) {
                $user = $service->getUserById($userId);
                if($user === null) {
                    $this->sendJSON([
                        "value" => $value,
                        "valid" => 0,
                        "message" => $translator->translate('Invalid username')            
                    ]); 
                } elseif($user->getEmail() == $value) {
                    $this->sendJSON([
                        "value" => $value,
                        "valid" => 1
                    ]);
                }
            }
            if($service->emailExists($value)) {
                $this->sendJSON(array(
                    "value" => $value,
                    "valid" => 0,
                    "message" => $translator->translate('This email is already registered')
                ));
            } else {
                $this->sendJSON(array(
                    "value" => $value,
                    "valid" => 1
                ));
            }
        }
        
        $this->sendJSON([
            "value" => $value,
            "valid" => 0,
            "message" => $translator->translate('Invalid e-mail address')            
        ]);       
    }
    
    public function editAction()
    {
        $translator = $this->getServiceProvider()->get('translator');
        $service = $this->getUsersService();
        $roles = $service->getRoles();     
        $userId = $this->getServiceProvider()->getRequest()->getAttribute('user_id');
        $user = $service->getUserById($userId);
        if(!$user) {
            $this->redirectToRoute('users_list');
            return;
        }
        
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {
            $v = new EditUserVerification($this->getServiceProvider()->getRequest());
            if($v->process()) {
                $updateUser = new User();
                $updateUser->setId($user->getId());
                $updateUser->setUsername($v->username);
                $updateUser->setEmail($v->email);
                $updateUser->setDisplayName($v->displayName);                
                
                $role = array_key_exists($v->role, $roles) ? $v->role : $service->getDefaultRole(); 
                $updateUser->setRole($role);                
                $updateUser->setLastLogin($user->getLastLogin());
                $updateUser->setCreated($user->getCreated());
                                
                if($updateUser->getUsername() != $user->getUsername() && 
                        $service->usernameExists($updateUser->getUsername())) {
                    $this->getView()->showAlert = $translator->translate("This username is already registered");
                } elseif($updateUser->getEmail() != $user->getEmail() && 
                        $service->emailExists($updateUser->getEmail())) {
                    $this->getView()->showAlert = $translator->translate("This email is already registered");
                } elseif($service->updateUser($updateUser)) {
                    $this->getView()->isSave = $translator->translate('The data is stored');
                    $user = $updateUser;
                }                  
            } else {
                $this->getView()->showAlert = $translator->translate("This form was completed correctly");
            }
        }
        
        $this->getView()->user = $user;
        $this->getView()->roles = $roles;
        echo $this->render();        
    }    
    
    public function deleteAction()
    {
        $service = $this->getUsersService();    
        $authService = $this->getServiceProvider()->get('auth_service');
        $userId = $this->getServiceProvider()->getRequest()->getAttribute('user_id');
        if($authService->getIdentity()->getId() == $userId) {
            $this->redirectToRoute('users_list');
            return;            
        }        
        
        $user = $service->getUserById($userId);
        if(!$user) {
            $this->redirectToRoute('users_list');
            return;
        }        
        
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {
            $params = $this->getServiceProvider()->getRequest()->getParsedBody();                        
            
            if(isset($params['del']) && ($params['del'] == 'true')) {
                $service->deleteUser($user);
            }
            
            $this->redirectToRoute('users_list');
            return;
        }        
        
        $this->getView()->user = $user;
        echo $this->render();
    }    
    
    public function sendJSON(array $data, $charset = 'utf-8')
    {
        header( 'Content-Type: application/json; charset=' . $charset );
        echo json_encode($data);
        die(); 
    }
    
    public function getUsersService()
    {
        if(!$this->usersService) {
            $this->usersService = $this->getServiceProvider()->get('users_service');
        }
        
        return $this->usersService;        
    }    
}