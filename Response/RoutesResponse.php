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
use JMS\I18nRoutingBundle\Router\I18nLoader;

class RoutesResponse
{
    private $baseUrl;
    private $routes;
    private $prefix;
    private $host;
    private $scheme;
    private $locale;
    private $localeOnly;

    public function __construct($baseUrl, RouteCollection $routes = null, $prefix = null, $host = null, $scheme = null, $locale = null, $localeOnly = false)
    {
        $this->baseUrl    = $baseUrl;
        $this->routes     = $routes ?: new RouteCollection();
        $this->prefix     = $prefix;
        $this->host       = $host;
        $this->scheme     = $scheme;
        $this->locale     = $locale;
        $this->localeOnly = $localeOnly;
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

            // Removes all the pefix if the route is I18n
            if (true === $this->localeOnly && false !== ($pos = mb_strpos($name, I18nLoader::ROUTING_PREFIX))) {
                // Check if the current route is valid to expose with the locale
                $i18nLocales = $route->getOption('i18n_locales');
                if ($i18nLocales && !in_array($this->locale, $i18nLocales)) {
                    continue;
                }

                $locale = $route->getDefault('_locale');
                if ($locale !== $this->locale) {
                    continue;
                }

                $name = substr($name, $pos + strlen(I18nLoader::ROUTING_PREFIX));
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

    public function getScheme()
    {
        return $this->scheme;
    }
}
