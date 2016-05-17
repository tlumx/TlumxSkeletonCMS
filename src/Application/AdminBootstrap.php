<?php

namespace Application;

use Tlumx\Bootstrapper;
use Tlumx\Translation\Translator;
use Application\Handler\AdminExceptionHandler;
use Application\Handler\AdminNotFoundHandler;
use Tlumx\Db\Db;
use Tlumx\Session\Session;
use Application\Entity\UserInterface;
use Application\Service\UsersService;
use Application\Security\AuthManager;
use Application\Auth\Authenticator\DbAuthenticator;
use Application\Auth\IdentityStorage\DbStorage;
use Tlumx\Router\Result as RouterResult;

class AdminBootstrap extends Bootstrapper
{
    public function init()
    {
        $this->getServiceProvider()->register('exception_handler', function ($c) {
            return new AdminExceptionHandler($c);
        });

        $this->getServiceProvider()->register('not_found_handler', function ($c) {
            return new AdminNotFoundHandler($c);
        }); 
        
        $this->getServiceProvider()->register('db', function ($c) {
            $dbOption = $c->getConfig('db');
            if(!isset($dbOption['username'])) {
                $dbOption['username'] = null;
            }            
            if(!isset($dbOption['password'])) {
                $dbOption['password'] = null;
            }                        
            if(!isset($dbOption['driver_options'])) {
                $dbOption['driver_options'] = null;
            }                        
            if(!isset($dbOption['enabled_profiler'])) {
                $dbOption['enabled_profiler'] = false;
            }            

            $db = new Db($dbOption['dsn'], $dbOption['username'], 
                    $dbOption['password'], $dbOption['driver_options'], $dbOption['enabled_profiler']);

            return $db;            
        });
        
        $this->getServiceProvider()->register('session', function ($c) {
            $config = $c->getConfig('session');
            $options = $config ? $config : [];                        
            $session = new Session($options);
            return $session;
        });        
        
        $this->getServiceProvider()->register('users_service', function ($c) {
            return new UsersService($c);
        });
        
        $this->getServiceProvider()->register('auth_service', function ($c) {
            $authenticator = new DbAuthenticator($c->get('users_service'));
            $storage = new DbStorage(null, $c->get('session'));
            $storage->setUsersService($c->get('users_service'));
            
            $auth = new AuthManager();
            $auth->setAuthenticator($authenticator);
            $auth->setIdentityStorage($storage);
            
            return $auth;
        });
    }
    
    public function postBootstrap()
    {
        $languages = new Languages();
        $languages->setLanguages($this->getConfig('languages'));
        $languages->setDefaultLanguage($this->getConfig('default_language'));
        $languages->setCurrentLanguage($this->getConfig('default_language'));        
        $this->getServiceProvider()->set('languages', $languages);
        
        $translator = new Translator();
        $translator->setLanguage($this->getConfig('default_language'));
        $namespaces = $this->getConfig('translator_files');
        foreach ($namespaces as $namespace => $files) {
            foreach ($files as $file) {
                $translator->addTranslationFile($file[0], $file[1], $file[2], $namespace);
            }
        }
        $this->getServiceProvider()->set('translator', $translator);
        
        $basePath = $this->getConfig('base_path');
        if(is_string($basePath) && $basePath) {
            $this->getServiceProvider()->getView()->basePath = '/' . trim($basePath, '/');
        } else {
            $this->getServiceProvider()->getView()->basePath = '';
        }
        $this->getServiceProvider()->getView()->setTitle($this->getConfig('title'));
        $this->getServiceProvider()->getView()->languages = $languages;
        $this->getServiceProvider()->getView()->t = $translator;
        $this->getServiceProvider()->getView()->router = $this->getServiceProvider()->getRouter();
        $this->getServiceProvider()->getView()->goToSiteLink = $this->getConfig('go_to_site_url');
        
        $modulesMenu = $this->getConfig('modules_menu');
        $this->getServiceProvider()->getView()->modulesMenu = $modulesMenu;
    }
    
    public function postRouting()
    {
        $result = $this->getServiceProvider()->getRequest()->getAttribute('router_result');
        $this->getServiceProvider()->getView()->currRoute = $result->getName();
        $this->getServiceProvider()->getView()->currRouteParams = $result->getParams();
    }
    
    public function preDispatch()
    {
        $routerResult = $this->getServiceProvider()->getRequest()->getAttribute('router_result');
        $currRouteName = $routerResult->getName();
        $authService = $this->getServiceProvider()->get('auth_service');
        
        if(!$authService->isLoggedIn()) {
            if(($currRouteName != 'account_login') &&
                ($currRouteName != 'account_forgot_password')) {
                // redirect to login
                $url = $this->getServiceProvider()->getRouter()->createPath('account_login');
                header('HTTP/1.1 302 Found');
                header('Location: '.$url);    
                exit();
            }    
        } elseif(($currRouteName == 'account_login') ||
                ($currRouteName == 'account_forgot_password')) {
            $url = $this->getServiceProvider()->getRouter()->createPath('index');
            header('HTTP/1.1 302 Found');
            header('Location: '.$url);
            exit();
        } else {
            $this->getServiceProvider()->getView()->userIdenty = $authService->getIdentity();
            // do autorization            
            $permissions = $this->getConfig('permissions');// $app->getConfig('permissions');
            $user = $authService->getIdentity();
            
            $handler = $routerResult->getHandler();        
            $handler['controller'] = isset($handler['controller']) ? $handler['controller'] : 'index'; 
            $handler['action'] = isset($handler['action']) ? $handler['action'] : 'index';            
            
            $permission = $handler['controller'].'::'.$handler['action'];
            if(!$this->_checkAuthorization($user, $permissions, $permission)) {
                $handler = [
                    'controller' => 'admin-index',
                    'action' => 'forbidden'
                ];
                $result = RouterResult::createSuccessful(
                        $routerResult->getName(), 
                        $handler, 
                        $routerResult->getParams(), 
                        $routerResult->getMiddlewares()
                );                                
                
                $request = $this->getServiceProvider()->getRequest()->withAttribute('router_result', $result);
                $this->getServiceProvider()->setRequest($request);
            }
        }        
    }    
    
    private function _checkAuthorization(UserInterface $user, array $permissions, $permission)
    {
        $role = $user->getRole();
        
        if(isset($permissions[$role])) {
            $rolePermissions = $permissions[$role];
            if(in_array($permission, $rolePermissions)) {
                return true;
            }
        }
        
        return false;
    }    
}