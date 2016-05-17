<?php

namespace Application;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractFormVerification
{
    protected $errors = [];
    
    protected $fields = [];
    
    protected $request;
    
    protected $requestParams;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;        
    }
    
    public function addError($key, $val)
    {
        $this->errors[$key][] = $val;
    }
    
    public function getError($key)
    {
        return ($this->hasError($key) ? $this->errors[$key] : null);
    }
    
    public function getErrors()
    {
        return $this->errors;
    }    
    
    public function hasError($key = null)
    {
        if($key != null) {
            return array_key_exists($key, $this->errors);
        }
        
        return (empty($this->errors) ? false : true);
    }    
    
    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }
    
    public function __get($name)
    {
        return (isset($this->fields[$name]) ? $this->fields[$name] : null);
    }
    
    public function getParam($key, $default = null)
    {
        if(!$this->requestParams) {
            $this->requestParams = $this->request->getParsedBody();
        }
        
        return isset($this->requestParams[$key]) ? $this->requestParams[$key] : $default;
    }    
    
    abstract function process();
}

