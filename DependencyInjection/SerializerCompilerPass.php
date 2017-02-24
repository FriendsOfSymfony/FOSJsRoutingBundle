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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class SerializerCompilerPass
 *
 * @author Miguel Angel Garzón <magarzon@gmail.com>
 */
class SerializerCompilerPass implements CompilerPassInterface
{
    const SERIALIZER_SERVICE_ID = 'fos_js_routing.serializer';

    /**
     * @inheritdoc
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(self::SERIALIZER_SERVICE_ID)
            || $container->hasAlias(self::SERIALIZER_SERVICE_ID)) {
            return;
        }

        if ($container->hasDefinition('serializer')) {
            $container->setAlias(self::SERIALIZER_SERVICE_ID, 'serializer');
        } else {
            $definition = $container->register(self::SERIALIZER_SERVICE_ID, Serializer::class);
            $normalizers = [
                $container->getDefinition('fos_js_routing.normalizer.route_collection'),
                $container->getDefinition('fos_js_routing.normalizer.routes_response'),
                $container->getDefinition('fos_js_routing.denormalizer.route_collection')
            ];
            $definition->addArgument($normalizers);

            $encoder = $container->register('fos_js_routing.encoder', JsonEncoder::class);
            $encoder->setPublic(false);
            $definition->addArgument(['json' => $encoder]);
        }
    }
}
