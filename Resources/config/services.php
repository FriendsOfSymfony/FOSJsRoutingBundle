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

use FOS\JsRoutingBundle\Command\DumpCommand;
use FOS\JsRoutingBundle\Command\RouterDebugExposedCommand;
use FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractor;
use FOS\JsRoutingBundle\Response\RoutesResponse;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set('fos_js_routing.extractor.class', ExposedRoutesExtractor::class)
        ->set('fos_js_routing.routes_response.class', RoutesResponse::class);

    $containerConfigurator->services()
        ->set('fos_js_routing.extractor', '%fos_js_routing.extractor.class%')
            ->public()
            ->args([
                service('fos_js_routing.router'),
                abstract_arg('routes to expose'),
                param('kernel.cache_dir'),
                param('kernel.bundles'),
            ])

        ->set('fos_js_routing.routes_response', '%fos_js_routing.routes_response.class%')
            ->public()

        ->set('fos_js_routing.dump_command', DumpCommand::class)
            ->tag('console.command')
            ->args([
                service('fos_js_routing.routes_response'),
                service('fos_js_routing.extractor'),
                service('fos_js_routing.serializer'),
                param('kernel.project_dir'),
                param('fos_js_routing.request_context_base_url'),
            ])
        
        ->set('fos_js_routing.router_debug_exposed_command', RouterDebugExposedCommand::class)
            ->tag('console.command')
            ->args([
                service('fos_js_routing.extractor'),
                service('router'),
            ]);
};
