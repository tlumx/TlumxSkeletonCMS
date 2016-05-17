<?php

namespace Pages;

use Tlumx\Bootstrapper;
use Pages\Service\PagesService;

class AdminBootstrap extends Bootstrapper
{
    public function init()
    {
        $this->getServiceProvider()->register('pages_service', function ($c) {
            return new PagesService($c);
        });
        
        $basePath = $this->getConfig('base_path');
        $lang = $this->getConfig('default_language');
        $moduleMenu = [
            'modules_menu' => [
                'Pages' => [
                    'Home' => $basePath.'/pages/home/'.$lang,
                    'About' => $basePath.'/pages/about/'.$lang
                ]
            ]
        ];
        $this->setConfig($moduleMenu); 
    }    
}