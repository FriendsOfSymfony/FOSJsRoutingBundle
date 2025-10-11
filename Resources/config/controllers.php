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

use FOS\JsRoutingBundle\Controller\Controller;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set('fos_js_routing.controller.class', Controller::class);

    $containerConfigurator->services()
        ->set('fos_js_routing.controller', '%fos_js_routing.controller.class%')
            ->public()
            ->args([
                service('fos_js_routing.routes_response'),
                service('fos_js_routing.serializer'),
                service('fos_js_routing.extractor'),
                param('fos_js_routing.cache_control'),
                param('kernel.debug'),
            ]);
};
