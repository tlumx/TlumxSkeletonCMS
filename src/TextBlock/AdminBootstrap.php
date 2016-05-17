<?php

namespace TextBlock;

use Tlumx\Bootstrapper;
use TextBlock\Service\TextBlockService;

class AdminBootstrap extends Bootstrapper
{
    public function init()
    {
        $this->getServiceProvider()->register('textblock_service', function ($c) {
            return new TextBlockService($c);
        });
        
        $basePath = $this->getConfig('base_path');
        $lang = $this->getConfig('default_language');
        $moduleMenu = [
            'modules_menu' => [
                'Text blocks' => [
                    'Information' => $basePath.'/textblock/information/'.$lang
                ]
            ]
        ];
        $this->setConfig($moduleMenu); 
    }    
}