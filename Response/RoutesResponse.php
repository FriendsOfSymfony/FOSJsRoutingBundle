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
    private $port;
    private $scheme;
    private $locale;
    private $domains;

    public function __construct($baseUrl, RouteCollection $routes = null, $prefix = null, $host = null, $port = null,
                                $scheme = null, $locale = null, $domains = array())
    {
        $this->baseUrl = $baseUrl;
        $this->routes  = $routes ?: new RouteCollection();
        $this->prefix  = $prefix;
        $this->host    = $host;
        $this->port    = $port;
        $this->scheme  = $scheme;
        $this->locale  = $locale;
        $this->domains = $domains;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getRoutes()
    {
        $exposedRoutes = array();
        foreach ($this->routes->all() as $name => $route) {

            if (!$route->hasOption('expose')) {
                $domain = 'default';
            } else {
                $domain = $route->getOption('expose');
                $domain = is_string($domain) ? ($domain === 'true' ? 'default' : $domain) : 'default';
            }


            if (count($this->domains) === 0) {
                if ($domain !== 'default') {
                    continue;
                }
            } elseif (!in_array($domain, $this->domains, true)) {
                continue;
            }

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
                'methods'      => $route->getMethods(),
                'schemes'      => $route->getSchemes(),
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

    public function getPort()
    {
        return $this->port;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
