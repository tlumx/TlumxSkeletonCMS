<?php

namespace Application\Controller;

use Tlumx\Controller;
use Application\Verification\LoginVerification;
use Application\Verification\UserProfileVerification;
use Application\Verification\ChangePasswordVerification;

class AccountController extends Controller
{
    protected $usersService;
    
    protected $authService;
    
    public function loginAction()
    {
        $this->enableLayout(false);
        $translator = $this->getServiceProvider()->get('translator');
        $authService = $this->getAuthService();
        
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {
            // TODO: remove
            sleep(3);
            $v = new LoginVerification($this->getServiceProvider()->getRequest());
            if($v->process()) {                
                if($authService->login($v->username, $v->password)) {
                    $usersService = $this->getServiceProvider()->get('users_service');
                    $user = $authService->getIdentity();
                    $user->setLastLogin(date($usersService::date_format, time()));
                    $usersService->updateUser($user);                    
                    
                    $this->sendJSON([
                        'error'=> false,
                        'message' => ''
                    ]);
                }
            }
            
            $this->sendJSON([
                'error'=> true,
                'message' => $translator->translate('Wrong username or password')
            ]);            
        }
        
        echo $this->render();
    }    
    
    public function logoutAction()
    {
         $authService = $this->getAuthService();
         $authService->logout();
         $this->redirectToRoute('account_login');
    }    
    
    public function userprofileAction()
    {
        $authService = $this->getAuthService();
        $service = $this->getUsersService();
        $user = $authService->getIdentity();
        $translator = $this->getServiceProvider()->get('translator');
        
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {
            $v = new UserProfileVerification($this->getServiceProvider()->getRequest());
            if($v->process()) {
                $updateUser = $user;
                $updateUser->setEmail($v->email);
                $updateUser->setDisplayName($v->displayName);
                
                if($user->getEmail() != $updateUser->getEmail() && 
                        $service->emailExists($updateUser->getEmail())) {
                        $this->getView()->showAlert = $translator->translate("This email is already registered");
                } elseif($service->updateUser($updateUser)) {
                    $this->getView()->isSave = $translator->translate('The data is stored');
                }
                
                $user = $updateUser;                
            }
        }
        
        $this->getView()->user = $user;
        echo $this->render();        
    }    
    
    public function changepasswordAction()
    {
        $service = $this->getUsersService();
        $authService = $this->getAuthService();
        $user = $authService->getIdentity();   
        $translator = $this->getServiceProvider()->get('translator');
        
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {
            $v = new ChangePasswordVerification($this->getServiceProvider()->getRequest());
            if($v->process()) {     
                if(!$service->verify($v->oldPassword, $user->getPassword())) {
                    $this->getView()->showAlert = $translator->translate("Invalid old password");
                } else {
                    $service->changePassword($user->getId(), $v->password);
                    $this->getView()->isSave = $translator->translate('The data is stored');
                }
            }
        }
        
        echo $this->render();
    }    
    
    public function forgotAction()
    {
        $this->enableLayout(false);        
        $error = '';        
        $translator = $this->getServiceProvider()->get('translator');
        $service = $this->getUsersService();        
        
        $query = $this->getServiceProvider()->getRequest()->getQueryParams();
        $action = strval(isset($query['action']) ? $query['action'] : '');
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {
            $action = 'submit';
        }        
        
        $post = $this->getServiceProvider()->getRequest()->getParsedBody(); 
        
        switch ($action) {
            case 'submit':
                $email = strval(isset($post['email']) ? $post['email'] : '');
                if(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $error = $translator->translate('Invalid e-mail address');
                } elseif($service->fetchPassword($email)) {
                    $this->redirectToRoute('account_forgot_password', [], ['action'=>'complete']);
                    return;
                } else {
                    $error = $translator->translate('Invalid e-mail address');
                }
                
                break;
            case 'complete':
                // nothing to do
                break;
            case 'confirm':
                $userId = strval(isset($query['id']) ? $query['id'] : '');
                $key = strval(isset($query['key']) ? $query['key'] : '');
                if($userId != '' && $key != '') {
                    if(!$service->confirmPassword($userId, $key)) {
                        $error = $translator->translate('Error confirmation of the new password');
                    }
                } else {
                    $error = $translator->translate('Error confirmation of the new password');
                }
                break;
        }        
        
        $this->getView()->action = $action;
        $this->getView()->error = $error;
        echo $this->render();                
    }
    
    public function sendJSON(array $data, $charset = 'utf-8')
    {
        header( 'Content-Type: application/json; charset=' . $charset );
        echo json_encode($data);
        die;        
    }    
    
    public function getUsersService()
    {
        if(!$this->usersService) {
            $this->usersService = $this->getServiceProvider()->get('users_service');
        }
        
        return $this->usersService;
    }

    public function getAuthService()
    {
        if(!$this->authService) {
            $this->authService = $this->getServiceProvider()->get('auth_service');
        }
        
        return $this->authService;
    }    
}