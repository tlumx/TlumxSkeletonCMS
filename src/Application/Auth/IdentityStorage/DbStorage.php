<?php

namespace Application\Auth\IdentityStorage;

use Application\Security\IdentityStorage\SessionStorage;
use Application\Service\UsersService;

class DbStorage extends SessionStorage
{
    protected $usersService;
    
    protected $user;

    public function setUsersService(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }
    
    public function getUsersService()
    {
        if(!$this->usersService) {
            throw new \RuntimeException('Application\Service\UsersService has not been set');
        }
        
        return $this->usersService;
    }
    
    public function getIdentity()
    {
        if(null !== $this->user) {
            return $this->user;
        }
        
        $identity = parent::getIdentity();
        
        $this->user = $this->getUsersService()->getUserByUsername($identity);
        
        return $this->user;
    }
    
    public function setIdentity($data)
    {        
        parent::setIdentity($data);
        $this->user = null;
    }
    
    public function clearIdentity()
    {
        parent::clearIdentity();
        $this->user = null;
    }
}