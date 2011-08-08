<?php

namespace FOS\JsRoutingBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * FOSJsRoutingExtension
 * Load configuration.
 *
 * @package     FOSJsRoutingBundle
 * @subpackage  DependencyInjection
 * @author      William DURAND <william.durand1@gmail.com>
 */
class FOSJsRoutingExtension extends Extension
{
    /**
     * Load configuration.
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('controllers.xml');

        $container
            ->getDefinition('fos.js_routing.extractor')
            ->replaceArgument(1, $config['routes_to_expose']);
    }
}
