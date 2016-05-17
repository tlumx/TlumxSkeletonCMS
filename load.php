<?php

$loader = require __DIR__ . '/vendor/autoload.php';

$loader->add('Application', __DIR__.DIRECTORY_SEPARATOR.'src');
$loader->add('Pages', __DIR__.DIRECTORY_SEPARATOR.'src');
$loader->add('TextBlock', __DIR__.DIRECTORY_SEPARATOR.'src');

$classMap = [
    'elFinder'                      => __DIR__ . '/src/php/elFinder.class.php',
    'elFinderConnector'             => __DIR__ . '/src/php/elFinderConnector.class.php',
    'elFinderVolumeDriver'          => __DIR__ . '/src/php/elFinderVolumeDriver.class.php',
    'elFinderVolumeLocalFileSystem' => __DIR__ . '/src/php/elFinderVolumeLocalFileSystem.class.php',
    'elFinderVolumeMySQL'           => __DIR__ . '/src/php/elFinderVolumeMySQL.class.php',    
];
$loader->addClassMap($classMap);