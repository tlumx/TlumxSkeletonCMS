<?php

namespace Application\Verification;

use Application\AbstractFormVerification;

class LoginVerification extends AbstractFormVerification
{
    public function process()
    {
        // validate the username  
        $this->username = strval($this->getParam('username'));
        if(!preg_match('/^[a-zA-Z][a-zA-Z0-9]{3,255}$/i', $this->username)) {
            $this->addError('username', "Invalid username.");
        }        
                
        // validate the password
        $this->password = trim(strval($this->getParam('password')));
        if(strlen($this->password) < 6) {
            $this->addError('password', "Invalid password.");
        }
        
        return !$this->hasError();
    }
}