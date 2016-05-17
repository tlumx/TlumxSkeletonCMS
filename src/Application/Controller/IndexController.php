<?php

namespace Application\Controller;

use Tlumx\Controller;
use Application\Verification\SendMailVerification;

class IndexController extends Controller
{
    public function indexAction()
    {
        $pageService = $this->getServiceProvider()->get('pages_service');
        $textBlockService = $this->getServiceProvider()->get('textblock_service');
        $currLang = $this->getServiceProvider()->get('languages')->getCurrentLanguage();

        $this->getView()->home = $pageService->getPage('home', $currLang);        
        $this->getView()->information = $textBlockService->getTextBlock('information', $currLang);
        echo $this->render();
    }
    
    public function aboutAction()
    {
        $pageService = $this->getServiceProvider()->get('pages_service');
        $currLang = $this->getServiceProvider()->get('languages')->getCurrentLanguage();
        $this->getView()->about = $pageService->getPage('about', $currLang);        
        echo $this->render();
    }
    
    public function contactAction()
    {
        $this->getView()->recaptcha_key = $this->getServiceProvider()->getConfig('recaptcha_key');
        echo $this->render();
    }    
    
    public function sendAction()
    {
        $t = $this->getServiceProvider()->get('translator');
        header('Content-type: application/json');
        
        if(!$this->getServiceProvider()->getRequest()->isXmlHttpRequest()) {            
            header('HTTP/1.1 500 Internal Server Error');
            $message = $t->translate('Ooops! Some error.');
            die(json_encode(array('message' => $message, 'error' => true)));            
        }
        
        $v = new SendMailVerification($this->getServiceProvider()->getRequest(), $this->getServiceProvider()->getConfig('recaptcha_secret_key'));
        if($v->process()) {
            if($this->sendMail($v->name, $v->email, $v->message)) {
                header_remove();
                header('Content-type: application/json');
                $message = $t->translate('Your message has been sent.');
                die(json_encode(array('message' => $message)));                    
            }
            
            $message = $t->translate('Ooops! Some error.');
            die(json_encode(array('message' => $message, 'error' => true)));            
        }
        
        $message = '';
        foreach ($v->getErrors() as $key => $errors) {
            foreach ($errors as $error) {
                $message .= "- " . $error . "<br>";
            }
        }
        die(json_encode(array('message' => $message, 'error' => true)));        
    }
    
    protected function sendMail($fromName, $fromEmail, $body)
    {
        $config = $this->getServiceProvider()->getConfig('email');
        
        $str = "<b><i>Site contacts message from:</i></b><br><b>Name:</b> ".$fromName."<br><b>Email:</b> ".$fromEmail;
        $str .= "<hr>";
        $body = $str . $body;
        
        $message = \Swift_Message::newInstance('Contacts message')
                ->setFrom(array($config['from_email'] => $config['from_name']))
                ->setTo(array($config['to_email'] => $config['to_name']))
                ->setBody($body, 'text/html');
        
        $transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
                ->setEncryption($config['encryption'])
                ->setUsername($config['username'])
                ->setPassword($config['password']);
        
        $mailer = \Swift_Mailer::newInstance($transport);
                
        try {
            $result = $mailer->send($message);
            if($result) {
                return true;
            }
            return false;           
        } catch (\Exception $e) {
            return false;
        }
    }
}