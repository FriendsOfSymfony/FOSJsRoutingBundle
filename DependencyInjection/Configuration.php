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

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration class.
 *
 * @author      William DURAND <william.durand1@gmail.com>
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
                ->scalarNode('serializer')->cannotBeEmpty()->end()
                ->arrayNode('routes_to_expose')
                    ->beforeNormalization()
                        ->ifTrue(function($v) { return !is_array($v); })
                        ->then(function($v) { return array($v); })
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('request_context_base_url')->defaultNull()->end()
            ->end();

        return $builder;
    }
}
