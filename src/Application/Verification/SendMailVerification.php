<?php

namespace Application\Verification;

use Application\AbstractFormVerification;
use Psr\Http\Message\ServerRequestInterface;

class SendMailVerification extends AbstractFormVerification
{
    protected $recaptchaKey;
    
    public function __construct(ServerRequestInterface $request, $recaptchaKey)
    {
        $this->recaptchaKey = $recaptchaKey;
        parent::__construct($request);       
    }    
    
    public function process()
    {
        $this->name = $this->getParam('name');
        if($this->name !== null) {
            $this->name = filter_var($this->name, FILTER_SANITIZE_STRING);
            if ($this->name == "" || strlen($this->name) < 2 || strlen($this->name) > 50) {
                $this->addError('name', "Please enter a valid name.");
            }
        } else {
            $this->addError('name', "Please enter your name.");
        }
        
        $this->email = $this->getParam('email');
        if ($this->email !== null) {
            $this->email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $this->addError('email', "Please enter a valid email address.");
            }
        } else {
            $this->addError('email', "Please enter your email address.");
        }
        
        $this->message = $this->getParam('message');
        if ($this->message !== null) {
            $this->message = filter_var($this->message, FILTER_SANITIZE_STRING);
            if ($this->message == "" || strlen($this->message) < 5 || strlen($this->message) > 999) {
                $this->addError('message', "Please enter a valid message to send.");
            }
        } else {
            $this->addError('message', "Please enter a message to send.");
        }
        
        
        $this->recaptcha = $this->getParam('g-recaptcha-response');
        if($this->recaptcha) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->recaptchaKey."&response=".$this->recaptcha."&remoteip=".$ip);
            $responseKeys = json_decode($response,true);
            if(intval($responseKeys["success"]) !== 1) {
                $this->addError('recaptcha', "reCAPTCHA checked failed!");
            }            
        } else {
            $this->addError('recaptcha', "Please check that you are not a robot.=".$this->recaptcha."!");
        }
        
        
        return !$this->hasError();
    }
}