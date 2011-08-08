<?php

namespace FOS\JsRoutingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration class.
 *
 * @package     FOSJsRoutingBundle
 * @subpackage  DependencyInjection
 * @author i    William DURAND <william.durand1@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $builder->root('fos_js_routing')
            ->children()
                ->arrayNode('routes_to_expose')
                    ->beforeNormalization()
                        ->ifTrue(function($v) { return !is_array($v); })
                        ->then(function($v) { return array($v); })
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $builder;
    }
}
