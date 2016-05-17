<?php

namespace Application\Handler;

use Tlumx\Handler\ExceptionHandlerInterface;
use Tlumx\ServiceProvider;

class ExceptionHandler implements ExceptionHandlerInterface
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
        $view->message = 'Internal Server Error';
        if($this->provider->getConfig('display_exceptions')) {
            $view->exception = $e;
        }
        
        $templates = $this->provider->getConfig('templates');        
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