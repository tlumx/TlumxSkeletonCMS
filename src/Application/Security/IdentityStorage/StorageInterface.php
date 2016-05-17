<?php

namespace Application\Security\IdentityStorage;

interface StorageInterface
{
    /**
     * Returns true if identy isset in storage
     * 
     * @return bool
     */
    public function hasIdentity();
    
    /**
     * Get identy data from storage
     * 
     * @return mixed
     */
    public function getIdentity();
    
    /**
     * Set identy data to storage
     * 
     * @param mixed $data
     */
    public function setIdentity($data);
    
    /**
     * Clear identy data from storage
     */
    public function clearIdentity();
}