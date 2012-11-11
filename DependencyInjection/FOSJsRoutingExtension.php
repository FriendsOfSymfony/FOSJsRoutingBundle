<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * FOSJsRoutingExtension
 * Load configuration.
 *
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

        if (isset($config['serializer'])) {
            $container->setAlias('fos_js_routing.serializer', new Alias($config['serializer'], false));
        } else {
            $loader->load('serializer.xml');
        }

        $container
            ->getDefinition('fos_js_routing.extractor')
            ->replaceArgument(1, $config['routes_to_expose']);

        if (isset($config['request_context_base_url'])) {
            $container->setParameter('fos_js_routing.request_context_base_url', $config['request_context_base_url']);
        }
    }
}
