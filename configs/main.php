<?php

return [
    'bootstrappers' => [
        'Application\Bootstrap'
    ],
    'controllers' => [
        'index' => 'Application\Controller\IndexController'
    ],
    'routes' => [
        'index' => [
            'methods' => ['GET'],
            'route' => '/',
            'handler' => [
                'controller' => 'index',
                'action' => 'index'
            ], 
        ],
        'index_lang' => [
            'methods' => ['GET'],
            'route' => '/{lang}',
            'handler' => [
                'controller' => 'index',
                'action' => 'index'                
            ],
            'filters' => ['lang' => '(\w{2})'],
        ],
        'about' => [
            'methods' => ['GET'],
            'route' => '/about',
            'handler' => [
                'controller' => 'index',
                'action' => 'about'
            ],            
        ],
        'about_lang' => [
            'methods' => ['GET'],
            'route' => '/{lang}/about',
            'handler' => [
                'controller' => 'index',
                'action' => 'about'
            ],
            'filters' => ['lang' => '(\w{2})'],
        ],        
        'contact' => [
            'methods' => ['GET'],
            'route' => '/contact',
            'handler' => [
                'controller' => 'index',
                'action' => 'contact'                
            ],            
        ],
        'contact_lang' => [
            'methods' => ['GET','POST'],
            'route' => '/{lang}/contact',
            'handler' => [
                'controller' => 'index',
                'action' => 'contact'                
            ],
            'filters' => ['lang' => '(\w{2})'],
        ],
        'contact_send_mail_lang' => [
            'methods' => ['POST'],
            'route' => '/{lang}/contact/send',
            'handler' => [
                'controller' => 'index',
                'action' => 'send'                
            ],
            'filters' => ['lang' => '(\w{2})'],
        ]
    ],
    'templates_paths' => [
        'index' => TEMPLATES_PATH . DS . 'index'
    ],
    'templates' => [
        'main' => TEMPLATES_PATH . DS . 'templates' .DS .'main.phtml',
        'template_error' => TEMPLATES_PATH . DS . 'templates' .DS .'template_error.phtml',
        'template_404' => TEMPLATES_PATH . DS . 'templates' .DS .'template_404.phtml',
        'template_405' => TEMPLATES_PATH . DS . 'templates' .DS .'template_405.phtml',
    ],
    'layout' => 'main',
    'display_exceptions' => true,
    'title' => 'Tlumx framework sample project',
    'languages' => [
        'en' => 'English',
        'es' => 'Español',
        'de' => 'Deutsch'
    ],
    'default_language' => 'en',    
    'translator_files' => array(
        'default' => array(
            array('array', LANGS_PATH . DS . 'en.php','en'),
            array('array', LANGS_PATH . DS . 'es.php','es'),
            array('array', LANGS_PATH . DS . 'de.php','de'),
        )
    ),    
    'recaptcha_key' => '',
    'recaptcha_secret_key' => '',
    'db' => include __DIR__ . DS . 'db.php',
    'email' => include __DIR__ . DS . 'email.php'     
];
