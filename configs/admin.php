<?php

return [
    'bootstrappers' => [
        'Application\AdminBootstrap',
        'Pages\AdminBootstrap',
        'TextBlock\AdminBootstrap'
    ],
    'controllers' => [
        'admin-index' => 'Application\Controller\AdminIndexController',
        'elfinder' => 'Application\Controller\ElFinderController',
        'users' => 'Application\Controller\UsersController',
        'account' => 'Application\Controller\AccountController',
        'pages' => 'Pages\Controller\PagesController',
        'textblock' => 'TextBlock\Controller\TextBlockController'
    ],
    'base_path' => '/admin',
    'routes' => include __DIR__ . DS . 'admin-routes.php',
    'templates_paths' => [
        'admin-index' => TEMPLATES_PATH . DS . 'index',
        'elfinder' => TEMPLATES_PATH . DS . 'elfinder',
        'users' => TEMPLATES_PATH . DS . 'users',
        'account' => TEMPLATES_PATH . DS . 'account',
        'pages' => TEMPLATES_PATH . DS . 'pages',
        'textblock' => TEMPLATES_PATH . DS . 'textblock',
    ],
    'templates' => [
        'admin' => TEMPLATES_PATH . DS . 'templates' .DS .'admin.phtml',
        'template_error' => TEMPLATES_PATH . DS . 'templates' .DS .'template_error.phtml',
        'template_404' => TEMPLATES_PATH . DS . 'templates' .DS .'template_404.phtml',
        'template_405' => TEMPLATES_PATH . DS . 'templates' .DS .'template_405.phtml',
        'template_clean_404' => TEMPLATES_PATH . DS . 'templates' .DS .'template_clean_404.phtml',
        'template_clean_error' => TEMPLATES_PATH . DS . 'templates' .DS .'template_clean_error.phtml',
        'mailfetchpassword' => TEMPLATES_PATH . DS . 'templates' .DS .'mailfetchpassword.phtml',
    ],
    'layout' => 'admin',
    'display_exceptions' => true,    
    'title' => 'Tlumx framework sample project',
    'site_url' => 'http://localhost/admin',
    'go_to_site_url' => 'http://localhost/',
    'languages' => [
        'en' => 'English',
        'es' => 'Español',
        'de' => 'Deutsch'
    ],
    'default_language' => 'en',    
    'translator_files' => array(
        'default' => array(
            array('array', LANGS_PATH . DS . 'en.php','en')
        )
    ),
    'elfinder' => array(
        'locale' => 'en',
        'connectorPath' => '/filemanager/connector',
        'roots'  => array(
            array(
                'driver' => 'LocalFileSystem',
                'path'   => UPLOADS_PATH . DS .'files' . DS,
                'URL'    => '/uploads/files/',
                'accessControl' => 'access',             // disable and hide dot starting files (OPTIONAL)                    
                'attributes' => array(
                    array( // hide readmes
                        'pattern' => '/images/',
                        'read' => false,
                        'write' => false,
                        'hidden' => true,
                        'locked' => false
                    ),
                ),                        
            )                    
        ),
    ),
    'db' => include __DIR__ . DS . 'db.php',
    'email' => include __DIR__ . DS . 'email.php', 
    'session' => [
        'save_path' => DATA_PATH . DS . 'session'
    ],
    
    'permissions' => [
        'admin' => [
            'admin-index::index',            
            'elfinder::index',
            'elfinder::connector',
            'elfinder::tinymce',
            'users::list',
            'users::create',
            'users::checkusername',
            'users::checkuseremail',
            'users::edit',
            'users::delete',
            'account::login',
            'account::logout',
            'account::forgot',
            'account::userprofile',
            'account::changepassword',
            'pages::home',
            'pages::about',
            'textblock::textblock',

        ],
        'manager' => [
            'admin-index::index',                        
            'account::login',
            'account::logout',
            'account::forgot',
            'account::userprofile',
            'account::changepassword',
            'pages::home',
            'pages::about',
            'textblock::textblock',            
        ]
    ]    
    
];