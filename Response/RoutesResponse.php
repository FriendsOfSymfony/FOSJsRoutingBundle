<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Response;

use Symfony\Component\Routing\RouteCollection;

class RoutesResponse
{
    private $baseUrl;
    private $routes;
    private $prefix;
    private $host;
    private $portextension;
    private $scheme;
    private $locale;

    public function __construct($baseUrl, RouteCollection $routes = null, $prefix = null, $host = null, $portextension = null, $scheme = null, $locale = null)
    {
        $this->baseUrl          = $baseUrl;
        $this->routes           = $routes ?: new RouteCollection();
        $this->prefix           = $prefix;
        $this->host             = $host;
        $this->portextension    = $portextension;
        $this->scheme           = $scheme;
        $this->locale           = $locale;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getRoutes()
    {
        $exposedRoutes = array();
        foreach ($this->routes->all() as $name => $route) {
            $compiledRoute = $route->compile();
            $defaults      = array_intersect_key(
                $route->getDefaults(),
                array_fill_keys($compiledRoute->getVariables(), null)
            );

            if (!isset($defaults['_locale']) && in_array('_locale', $compiledRoute->getVariables())) {
                $defaults['_locale'] = $this->locale;
            }

            $exposedRoutes[$name] = array(
                'tokens'       => $compiledRoute->getTokens(),
                'defaults'     => $defaults,
                'requirements' => $route->getRequirements(),
                'hosttokens'   => method_exists($compiledRoute, 'getHostTokens') ? $compiledRoute->getHostTokens() : array(),
            );
        }

        return $exposedRoutes;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPortExtension()
    {
        return $this->portextension;
    }

    public function getScheme()
    {
        return $this->scheme;
    }
}
