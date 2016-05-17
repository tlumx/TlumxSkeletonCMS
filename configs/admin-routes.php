<?php

return [
    'index' => [
        'methods' => ['GET'],
        'route' => '/',
        'handler' => [
            'controller' => 'admin-index',
            'action' => 'index'
        ],            
    ],
    'filemanager' => [
        'methods' => ['GET'],
        'route' => '/filemanager',
        'handler' => [
            'controller' => 'elfinder',
            'action' => 'index'
        ],            
    ],
    'filemanager_connector' => [
        'methods' => ['GET', 'POST'],
        'route' => '/filemanager/connector',
        'handler' => [
            'controller' => 'elfinder',
            'action' => 'connector'
        ],
    ],
    'filemanager_tinymce' => [
        'methods' => ['GET'],
        'route' => '/filemanager/tinymce',
        'handler' => [
            'controller' => 'elfinder',
            'action' => 'tinymce'
        ],
    ],
    'users_list' => [
        'methods' => 'GET',
        'route' => '/users',
        'handler' => [
            'controller' => 'users',
            'action' => 'list'
        ],            
    ],        
    'users_add' => [
        'methods' => ['GET', 'POST'],
        'route' => '/users/create',
        'handler' => [
            'controller' => 'users',
            'action' => 'create'
        ],             
    ],        
    'users_check_username' => [
        'methods' => 'GET',
        'route' => '/users/checkusername/{user_id}',
        'handler' => [
            'controller' => 'users',
            'action' => 'checkusername'
        ],            
        'filters' => [
            'user_id' => '(\d+)'
        ],                    
    ],
    'users_check_email' => [
        'methods' => 'GET',
        'route' => '/users/checkuseremail/{user_id}',
        'handler' => [
            'controller' => 'users',
            'action' => 'checkuseremail'
        ],            
        'filters' => [
            'user_id' => '(\d+)'
        ],                     
    ],        
    'users_edit' => [
        'methods' => ['GET', 'POST'],
        'route' => '/users/edit/{user_id}',
        'handler' => [
            'controller' => 'users',
            'action' => 'edit'
        ],            
        'filters' => [
            'user_id' => '(\d+)'
        ],                    
    ],
    'users_delete' => [
        'methods' => ['GET', 'POST'],
        'route' => '/users/delete/{user_id}',
        'handler' => [
            'controller' => 'users',
            'action' => 'delete'
        ],            
        'filters' => [
            'user_id' => '(\d+)'
        ],                    
    ],
    'account_login' => [
        'methods' => ['GET', 'POST'],
        'route' => '/login',
        'handler' => [
            'controller' => 'account',
            'action' => 'login'
        ],
    ],
    'account_logout' => [
        'methods' => ['GET', 'POST'],
        'route' => '/logout',
        'handler' => [
            'controller' => 'account',
            'action' => 'logout'
        ],
    ],        
    'account_forgot_password' => [
        'methods' => ['GET', 'POST'],
        'route' => '/forgot',
        'handler' => [
            'controller' => 'account',
            'action' => 'forgot'
        ],            
    ],
    'account_profile' => [
        'methods' => ['GET', 'POST'],
        'route' => '/userprofile',
        'handler' => [
            'controller' => 'account',
            'action' => 'userprofile'
        ],             
    ],        
    'account_change_password' => [
        'methods' => ['GET', 'POST'],
        'route' => '/changepassword',
        'handler' => [
            'controller' => 'account',
            'action' => 'changepassword'
        ],
    ],
    'pages_about' => [
        'methods' => ['GET', 'POST'],
        'route' => '/pages/about/{lang}',
        'handler' => [
            'controller' => 'pages',
            'action' => 'about'
        ],            
        'filters' => [
            'lang' => '(\w+)'
        ],
    ],
    'pages_home' => [
        'methods' => ['GET', 'POST'],
        'route' => '/pages/home/{lang}',
        'handler' => [
            'controller' => 'pages',
            'action' => 'home'
        ],            
        'filters' => [
            'lang' => '(\w+)'
        ],
    ],        
    'textblock_information' => [
        'methods' => ['GET', 'POST'],
        'route' => '/textblock/information/{lang}',
        'handler' => [
            'controller' => 'textblock',
            'action' => 'textblock',
            'block' => 'information',
        ],            
        'filters' => [
            'lang' => '(\w+)'
        ],
    ],     
];