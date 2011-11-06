<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Extractor;

use Symfony\Component\Routing\RouterInterface;

/**
 * @author      William DURAND <william.durand1@gmail.com>
 */
class ExposedRoutesExtractor implements ExposedRoutesExtractorInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * Default constructor.
     *
     * @param \Symfony\Component\Routing\RouterInterface $router  The router.
     * @param array $routesToExpose Some route names to expose.
     */
    public function __construct(RouterInterface $router, array $routesToExpose = array())
    {
        $this->router = $router;
        $this->routesToExpose = $routesToExpose;
    }

    /**
     * Returns an array of exposed routes where keys are the route names.
     *
     * @return array
     */
    public function getRoutes()
    {
        $exposedRoutes = array();
        foreach ($this->getExposedRoutes() as $name => $route) {
            // Maybe there is a better way to do that...
            $compiledRoute = $route->compile();
            $defaults = array_intersect_key(
                $route->getDefaults(),
                array_fill_keys($compiledRoute->getVariables(), null)
            );

            $exposedRoutes[$name] = new ExtractedRoute(
                $compiledRoute->getTokens(),
                $defaults
            );
        }

        return $exposedRoutes;
    }

    /**
     * Returns an array of all exposed Route objects.
     *
     * @return array
     */
    public function getExposedRoutes()
    {
        $routes     = array();
        $collection = $this->router->getRouteCollection();
        $pattern    = $this->buildPattern();

        foreach ($collection->all() as $name => $route) {
            if (false === $route->getOption('expose')) {
                continue;
            }

            if (($route->getOption('expose') && (true === $route->getOption('expose') || 'true' === $route->getOption('expose')))
                || ('' !== $pattern && preg_match('#' . $pattern . '#', $name))) {
                $routes[$name] = $route;
            }
        }

        return $routes;
    }

    /**
     * Returns the Base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->router->getContext()->getBaseUrl() ?: '';
    }

    /**
     * Returns an array of routing resources.
     *
     * @return array
     */
    public function getResources()
    {
        return $this->router->getRouteCollection()->getResources();
    }

    /**
     * Convert the routesToExpose array in a regular expression pattern.
     *
     * @return string
     */
    protected function buildPattern()
    {
        $patterns = array();
        foreach ($this->routesToExpose as $toExpose) {
            $patterns[] = '(' . $toExpose . ')';
        }
        return implode($patterns, '|');
    }
}
