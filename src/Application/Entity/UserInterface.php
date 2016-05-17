<?php

namespace Application\Entity;

interface UserInterface
{
    public function getId();
    
    public function setId($id);
    
    public function getUsername();
    
    public function setUsername($username);
    
    public function getEmail();
    
    public function setEmail($email);
    
    public function getDisplayName();
    
    public function setDisplayName($displayName);
    
    public function getPassword();
    
    public function setPassword($password);
    
    public function getRole();
    
    public function setRole($role);
    
    public function getLastLogin();
    
    public function setLastLogin($lastLogin);
    
    public function getCreated();
    
    public function setCreated($created);                        
}