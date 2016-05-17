<?php

namespace Application\Verification;

use Application\AbstractFormVerification;

class ChangePasswordVerification extends AbstractFormVerification
{
    public function process()
    {
        // validate the password
        $this->oldPassword = trim(strval($this->getParam('old_password')));
        if(strlen($this->oldPassword) < 6) {
            $this->addError('old_password', "Invalid old password.");
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
        
        return !$this->hasError();
    }
}