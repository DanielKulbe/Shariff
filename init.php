<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

# local extension path has no autoloader
spl_autoload_register(function ($class) {
    $class = preg_replace('/\\\\/', DIRECTORY_SEPARATOR, str_replace(__NAMESPACE__, null, $class));
    $classFile = __DIR__.DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class . '.php';

    if (file_exists($classFile))
        include $classFile;
});
# ###

$app['extensions']->register(new Extension($app));
