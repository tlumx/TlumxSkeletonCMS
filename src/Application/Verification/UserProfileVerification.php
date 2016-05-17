<?php

namespace Application\Verification;

use Application\AbstractFormVerification;

class UserProfileVerification extends AbstractFormVerification
{
    public function process()
    {       
        
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

        return !$this->hasError();
    }
}