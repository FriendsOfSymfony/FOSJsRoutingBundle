<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Loader\XmlFileLoader;

return function (RoutingConfigurator $routes): void {
    foreach (debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT) as $trace) {
        if (isset($trace['object']) && $trace['object'] instanceof XmlFileLoader && 'doImport' === $trace['function']) {
            if (__DIR__ === dirname(realpath($trace['args'][3]))) {
                trigger_deprecation('friendsofsymfony/jsrouting-bundle', '3.6', 'The "routing-sf4.xml" routing configuration file is deprecated, import "routing.php" instead.');

                break;
            }
        }
    }

    $routes->add('fos_js_routing_js', '/js/routing.{_format}')
        ->methods(['GET'])
        ->controller('fos_js_routing.controller::indexAction')
        ->requirements(['_format' => 'js|json'])
        ->defaults(['_format' => 'js'])
    ;
};
