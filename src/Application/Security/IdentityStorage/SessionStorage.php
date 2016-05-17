<?php

namespace Application\Security\IdentityStorage;

use Tlumx\Session\Session;

class SessionStorage implements StorageInterface
{
    protected $identyKey = 'Tlumx_Framework_Application_Auth';

    protected $session;   

    public function __construct($identyKey = null, Session $session = null)
    {
        if($identyKey !== null) {
            $this->identyKey = $identyKey;
        }
        
        if($session !== null) {
            $this->session = $session;
        } else {
            $this->session = new Session();
        }        
    }
    
    public function hasIdentity()
    {
        return $this->session->has($this->identyKey);
    }
    
    public function getIdentity()
    {
        return $this->session->get($this->identyKey);
    }
    
    public function setIdentity($data)
    {
        $this->session->set($this->identyKey, $data);
    }
    
    public function clearIdentity()
    {
        $this->session->remove($this->identyKey);
    }
}