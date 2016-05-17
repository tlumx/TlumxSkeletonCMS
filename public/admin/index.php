<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('TEMPLATES_PATH', ROOT_PATH . DS . 'templates' . DS . 'admin');
define('DATA_PATH', ROOT_PATH . DS . 'data');
define('LANGS_PATH', DATA_PATH . DS . 'admin-languages');
define('PABLIC_PATH', dirname(__DIR__));
define('UPLOADS_PATH', PABLIC_PATH . DS . 'uploads');

chdir(dirname(dirname(__DIR__)));
require 'load.php';

use Tlumx\Application;

$app = new Application(include ROOT_PATH . DS . 'configs' . DS . 'admin.php');

$app->run();