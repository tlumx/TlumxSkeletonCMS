<?php

namespace Application\Verification;

use Application\AbstractFormVerification;

class CreateUserVerification extends AbstractFormVerification
{
    public function process()
    {
        // validate the username
        $this->username = strval($this->getParam('username'));
        if(!preg_match('/^[a-zA-Z][a-zA-Z0-9]{3,255}$/i', $this->username)) {
            $this->addError('username', "Invalid username.");
        }       
        
        // validate the e-mail address
        $this->email = strval($this->getParam('email'));
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', "Invalid email address.");
        }
        
        // validate the display name
        $this->displayName = trim(strip_tags($this->getParam('display_name')));
        $this->displayName = preg_replace('/\s{2,}/', ' ', $this->displayName);
        $this->displayName = htmlspecialchars($this->displayName);
        if(strlen($this->displayName) < 4 && strlen($this->displayName) > 255) {
            $this->addError('display_name', "Invalid display name.");
        }
        
        // validate the password
        $this->password = trim(strval($this->getParam('password')));
        if(strlen($this->password) < 6) {
            $this->addError('password', "Invalid password.");
        }
        
        // validate the password
        $this->passwordConfirm = trim(strval($this->getParam('password_confirm')));
        if($this->passwordConfirm !== $this->password) {
            $this->addError('password', "Invalid password confirm.");
        }     
        
        //roles
        $this->role = trim(strip_tags($this->getParam('role')));
        
        return !$this->hasError();
    }
}