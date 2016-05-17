<?php

namespace Application\Handler;

use Tlumx\Handler\NotFoundHandlerInterface;
use Tlumx\ServiceProvider;

class AdminNotFoundHandler implements NotFoundHandlerInterface
{
    protected $provider;

    public function __construct(ServiceProvider $provider)
    {
        $this->provider = $provider;
    }
    
    public function handle(array $allowedMethods = [])
    {
        $response = $this->provider->getResponse();
        $view = $this->provider->getView();
        $templates = $this->provider->getConfig('templates');
        
        $authService = $this->provider->get('auth_service');
        if(!$authService->isLoggedIn()) {
            $response = $response->withStatus(404);
            $view->siteUrl = $this->provider->getRouter()->createPath('account_login');
            $result = $view->renderFile($templates['template_clean_404']);
            $response->getBody()->write($result);
            $response->withHeader('Content-Type', 'text/html');
            return $response;            
        }        
                
        if(empty($allowedMethods)) {
            $response = $response->withStatus(404);
            $message = 'Page not found';
            $template = 'template_404';
        } else {
            $response = $response->withStatus(405);
            $message = 'Method Not Allowed';
            $template = 'template_405';
        }        
                
        $view->userIdenty = $authService->getIdentity();
        $view->message = $message;    
        $result = $view->renderFile($templates[$template]);
        if($this->provider->getConfig('layout') !== null) {
            $view->content = $result;
            $result = $view->renderFile($templates[$this->provider->getConfig('layout')]);
        }        
        
        $response->getBody()->write($result);
        $response->withHeader('Content-Type', 'text/html');
        return $response;       
    }
}