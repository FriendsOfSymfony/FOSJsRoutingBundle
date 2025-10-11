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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\JsRoutingBundle\Serializer\Denormalizer\RouteCollectionDenormalizer;
use FOS\JsRoutingBundle\Serializer\Normalizer\RouteCollectionNormalizer;
use FOS\JsRoutingBundle\Serializer\Normalizer\RoutesResponseNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set('fos_js_routing.normalizer.route_collection.class', RouteCollectionNormalizer::class)
        ->set('fos_js_routing.normalizer.routes_response.class', RoutesResponseNormalizer::class)
        ->set('fos_js_routing.denormalizer.route_collection.class', RouteCollectionDenormalizer::class);

    $containerConfigurator->services()
        ->set('fos_js_routing.serializer', Serializer::class)
            ->public()
            ->args([
                [
                    service('fos_js_routing.normalizer.route_collection'),
                    service('fos_js_routing.normalizer.routes_response'),
                    service('fos_js_routing.denormalizer.route_collection'),
                ],
                [
                    'json' => service('fos_js_routing.encoder'),
                ],
            ])

        ->set('fos_js_routing.normalizer.route_collection', '%fos_js_routing.normalizer.route_collection.class%')

        ->set('fos_js_routing.normalizer.routes_response', '%fos_js_routing.normalizer.routes_response.class%')

        ->set('fos_js_routing.denormalizer.route_collection', '%fos_js_routing.denormalizer.route_collection.class%')

        ->set('fos_js_routing.encoder', JsonEncoder::class);
};
