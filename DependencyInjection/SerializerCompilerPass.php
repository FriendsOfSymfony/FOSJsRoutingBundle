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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SerializerCompilerPass
 *
 * @author Miguel Angel Garzón <magarzon@gmail.com>
 */
class SerializerCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('fos_js_routing.serializer')
            || $container->hasAlias('fos_js_routing.serializer')) {
            return;
        }

        if ($container->hasDefinition('serializer')) {
            $container->setAlias('fos_js_routing.serializer', 'serializer');
        } else {
            $definition = $container->register('fos_js_routing.serializer', 'Symfony\Component\Serializer\Serializer');
            $normalizers = array(
                $container->getDefinition('fos_js_routing.normalizer.route_collection'),
                $container->getDefinition('fos_js_routing.normalizer.routes_response'),
                $container->getDefinition('fos_js_routing.denormalizer.route_collection')
            );
            $definition->addArgument($normalizers);

            $encoder = $container->register('fos_js_routing.encoder', 'Symfony\Component\Serializer\Encoder\JsonEncoder');
            $encoder->setPublic(false);
            $definition->addArgument(array('json' => $encoder));
        }
    }
}
