<?php

namespace Application\Security\Authenticator;

class Simple implements AuthenticatorInterface
{
    protected $users = [];

    public function __construct(array $users)
    {
        $this->users = $users;
    }
    
    public function authenticate($identity, $credential)
    {        
        if(!array_key_exists($identity, $this->users)) {
            return false;
        }
        
        if($credential != $this->users[$identity]) {
            return false;
        }
        
        return true;
    }
}