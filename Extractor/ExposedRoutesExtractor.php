<?php

namespace Bazinga\ExposeRoutingBundle\Extractor;

use Symfony\Component\Routing\RouterInterface;

/**
 * @package     ExposeRoutingBundle
 * @subpackage  Extractor
 * @author William DURAND <william.durand1@gmail.com>
 */
class ExposedRoutesExtractor implements ExposedRoutesExtractorInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * Default constructor.
     * @param \Symfony\Component\Routing\RouterInterface  The router.
     * @param array Some route names to expose.
     */
    public function __construct(RouterInterface $router, array $routesToExpose = array())
    {
        $this->router = $router;
        $this->routesToExpose = $routesToExpose;
    }

    /**
     * Returns an array of exposed routes where keys are the route names.
     * @return array
     */
    public function getRoutes()
    {
        $exposed_routes = array();
        $collection     = $this->router->getRouteCollection();
        $pattern        = $this->buildPattern();

        foreach ($collection->all() as $name => $route) {
            if (false === $route->getOption('expose')) {
                continue;
            }

            if (($route->getOption('expose') && true === $route->getOption('expose'))
                || ('' !== $pattern && preg_match('#' . $pattern . '#', $name))) {
                // Maybe there is a better way to do that...
                $compiledRoute = $route->compile();
                $route->setDefaults(array_intersect_key(
                    $route->getDefaults(),
                    array_fill_keys($compiledRoute->getVariables(), null)
                ));

                $exposed_routes[$name] = $route;
            }
        }

        return $exposed_routes;
    }

    /**
     * Returns the Base URL.
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->router->getContext()->getBaseUrl() ?: '';
    }

    /**
     * Convert the routesToExpose array in a regular expression pattern.
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
