<?php

namespace Application\Handler;

use Tlumx\Handler\ExceptionHandlerInterface;
use Tlumx\ServiceProvider;

class AdminExceptionHandler implements ExceptionHandlerInterface
{
    protected $provider;

    public function __construct(ServiceProvider $provider)
    {
        $this->provider = $provider;
    }
    
    public function handle(\Exception $e)
    {        
        $response = $this->provider->getResponse();
        $response = $response->withStatus(500);
        
        $view = $this->provider->getView();
        $templates = $this->provider->getConfig('templates');
        $authService = $this->provider->get('auth_service');
        if(!$authService->isLoggedIn()) {
            $view->siteUrl = $this->provider->getRouter()->createPath('account_login');
            $result = $view->renderFile($templates['template_clean_error']);
            $response->getBody()->write($result);
            $response->withHeader('Content-Type', 'text/html');
            return $response;            
        }        
                     
        $view->userIdenty = $authService->getIdentity();
        $view->message = 'Internal Server Error';
        if($this->provider->getConfig('display_exceptions')) {
            $view->exception = $e;
        }
                        
        $result = $view->renderFile($templates['template_error']);
        if($this->provider->getConfig('layout') !== null) {
            $view->content = $result;
            $result = $view->renderFile($templates[$this->provider->getConfig('layout')]);
        }        
        
        $response->getBody()->write($result);
        $response->withHeader('Content-Type', 'text/html');
        return $response;
    }		
}