<?php

namespace Application;

use Tlumx\Bootstrapper;
use Tlumx\Db\Db;
use Tlumx\Translation\Translator;
use Application\Handler\ExceptionHandler;
use Application\Handler\NotFoundHandler;
use Pages\Service\PagesService;
use TextBlock\Service\TextBlockService;

class Bootstrap extends Bootstrapper
{
    public function init()
    {
        $this->getServiceProvider()->register('exception_handler', function ($c) {
            return new ExceptionHandler($c);
        });

        $this->getServiceProvider()->register('not_found_handler', function ($c) {
            return new NotFoundHandler($c);
        });
        
        $this->getServiceProvider()->register('pages_service', function ($c) {
            return new PagesService($c);
        });
        
        $this->getServiceProvider()->register('textblock_service', function ($c) {
            return new TextBlockService($c);
        });        
        
        $this->getServiceProvider()->register('db', function ($c) {
            $dbOption = $c->getConfig('db');
            if(!isset($dbOption['username'])) {
                $dbOption['username'] = null;
            }            
            if(!isset($dbOption['password'])) {
                $dbOption['password'] = null;
            }                        
            if(!isset($dbOption['driver_options'])) {
                $dbOption['driver_options'] = null;
            }                        
            if(!isset($dbOption['enabled_profiler'])) {
                $dbOption['enabled_profiler'] = false;
            }            

            $db = new Db($dbOption['dsn'], $dbOption['username'], 
                    $dbOption['password'], $dbOption['driver_options'], $dbOption['enabled_profiler']);

            return $db;            
        });        
    }

    public function postBootstrap()
    {
        $languages = new Languages();
        $languages->setLanguages($this->getConfig('languages'));
        $languages->setDefaultLanguage($this->getConfig('default_language'));
        $languages->setCurrentLanguage($this->getConfig('default_language'));        
        $this->getServiceProvider()->set('languages', $languages);
        
        $translator = new Translator();
        $translator->setLanguage($this->getConfig('default_language'));
        $namespaces = $this->getConfig('translator_files');
        foreach ($namespaces as $namespace => $files) {
            foreach ($files as $file) {
                $translator->addTranslationFile($file[0], $file[1], $file[2], $namespace);
            }
        }
        $this->getServiceProvider()->set('translator', $translator);
               
        $basePath = $this->getConfig('base_path');
        if(is_string($basePath) && $basePath) {
            $this->getServiceProvider()->getView()->basePath = '/' . trim($basePath, '/');
        } else {
            $this->getServiceProvider()->getView()->basePath = '';
        }
        $this->getServiceProvider()->getView()->setTitle($this->getConfig('title'));
        $this->getServiceProvider()->getView()->languages = $languages;
        $this->getServiceProvider()->getView()->t = $translator;
        $this->getServiceProvider()->getView()->router = $this->getServiceProvider()->getRouter();
    }
    
    public function postRouting()
    {
        $lang = $this->getServiceProvider()->getRequest()->getAttribute('lang');
        $languages = $this->getServiceProvider()->get('languages');        
        if(array_key_exists($lang, $languages->getLanguages())) {
            $languages->setCurrentLanguage($lang);
            $this->getServiceProvider()->get('translator')->setLanguage($lang);
        }
        
        $result = $this->getServiceProvider()->getRequest()->getAttribute('router_result');
        $this->getServiceProvider()->getView()->currRoute = $result->getName();
        $this->getServiceProvider()->getView()->currRouteParams = $result->getParams();
    }
}