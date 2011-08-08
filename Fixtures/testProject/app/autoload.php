<?php

require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'     => __DIR__.'/../vendor/symfony/src',
));
$loader->registerPrefixes(array(
    'Twig_Extensions_'   => __DIR__.'/../vendor/twig-extensions/lib',
    'Twig_'              => __DIR__.'/../vendor/twig/lib',
));
$loader->register();

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'FOS\JsRoutingBundle\\')) {
        $path = implode('/', array_slice(explode('\\', $class), 2)).'.php';
        require_once __DIR__.'/../../../'.$path;
        return true;
    }
});
