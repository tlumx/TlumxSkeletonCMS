<?php

namespace Application\Security;

use Application\Security\IdentityStorage\StorageInterface;
use Application\Security\IdentityStorage\SessionStorage;
use Application\Security\Authenticator\AuthenticatorInterface;

class AuthManager
{
    protected $identityStorage;
    
    protected $authenticator;

    public function getIdentityStorage()
    {
        if(!$this->identityStorage) {
            $this->identityStorage = new SessionStorage();
        }
        
        return $this->identityStorage;
    }
    
    public function setIdentityStorage(StorageInterface $identityStorage)
    {
        $this->identityStorage = $identityStorage;
    }
    
    public function getAuthenticator()
    {
        if(!$this->authenticator) {
            throw new \RuntimeException('Authenticator is not isset');
        }
        
        return $this->authenticator;
    }
    
    public function setAuthenticator(AuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;
    }
    
    public function login($identity, $credential)
    {
        $result = $this->getAuthenticator()->authenticate($identity, $credential);
        
        if($this->getIdentityStorage()->hasIdentity()) {
            $this->getIdentityStorage()->clearIdentity();
        }
                
        if($result === false) {
            return false;
        }
                
        $this->getIdentityStorage()->setIdentity($identity);
        return true;
    }
    
    public function isLoggedIn()
    {
        return $this->getIdentityStorage()->hasIdentity();
    }
    
    public function logout()
    {
        $this->getIdentityStorage()->clearIdentity();
    }
    
    public function getIdentity()
    {
        if(!$this->getIdentityStorage()->hasIdentity()) {
            return null;
        }
        
        return $this->getIdentityStorage()->getIdentity();
    }    
}