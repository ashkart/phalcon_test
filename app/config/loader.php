<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir
    ]
);

$loader->registerNamespaces([
    'App'            => $config->application->appDir,
    'App\Models'     => $config->application->modelsDir,
    'App\Controller' => $config->application->controllersDir,
    'App\Validator'  => $config->application->modelsDir . 'validators/',
    'App\Lib\Http'   => $config->application->libraryDir . 'http'
]);

$loader->register();