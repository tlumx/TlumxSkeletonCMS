<?php

namespace Application\Auth\Authenticator;

use Application\Security\Authenticator\AuthenticatorInterface;
use Application\Service\UsersService;

class DbAuthenticator implements AuthenticatorInterface
{
    protected $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }
    
    public function authenticate($username, $password)
    {        
        $result = false;
        
        $user = $this->usersService->getUserByUsername($username);
        if($user) {
            $result = $this->usersService->verify($password, $user->getPassword());
        }
        
        return $result;
    }
}