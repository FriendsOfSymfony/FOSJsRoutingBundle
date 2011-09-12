<?php

$subPath = 'vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
$dir = $lastDir = __DIR__;
while (($lastDir !== $dir = dirname($dir))
       && !file_exists($dir.'/'.$subPath)
       && $lastDir = $dir);

require_once $dir.'/'.$subPath;

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'     => $dir.'/vendor/symfony/src',
));
$loader->register();

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'FOS\JsRoutingBundle\\')) {
        $path = implode('/', array_slice(explode('\\', $class), 2)).'.php';
        require_once __DIR__.'/../../../'.$path;
        return true;
    }
});
