<?php

declare(strict_types=1);

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('fos_js_routing');

        $rootNode = $builder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('serializer')->cannotBeEmpty()->end()
                ->arrayNode('routes_to_expose')
                    ->beforeNormalization()
                        ->ifTrue(fn ($v) => !is_array($v))
                        ->then(fn ($v) => [$v])
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('router')->defaultValue('router')->end()
                ->scalarNode('request_context_base_url')->defaultNull()->end()
                ->arrayNode('cache_control')
                    ->children()
                        ->booleanNode('public')->defaultFalse()->end()
                        ->scalarNode('expires')->defaultNull()->end()
                        ->scalarNode('maxage')->defaultNull()->end()
                        ->scalarNode('smaxage')->defaultNull()->end()
                        ->arrayNode('vary')
                            ->beforeNormalization()
                                ->ifTrue(fn ($v) => !is_array($v))
                                ->then(fn ($v) => [$v])
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
