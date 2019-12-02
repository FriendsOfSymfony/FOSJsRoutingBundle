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

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use JMS\I18nRoutingBundle\Router\I18nLoader;

/**
 * @author      William DURAND <william.durand1@gmail.com>
 */
class ExposedRoutesExtractor implements ExposedRoutesExtractorInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * Base cache directory
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * @var array
     */
    protected $bundles;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var array
     */
    protected $availableDomains;

    /**
     * Default constructor.
     *
     * @param RouterInterface $router The router.
     * @param array $routesToExpose Some route names to expose.
     * @param string $cacheDir
     * @param array $bundles list of loaded bundles to check when generating the prefix
     *
     * @throws \Exception
     */
    public function __construct(RouterInterface $router, array $routesToExpose = array(), $cacheDir, $bundles = array())
    {
        $this->router         = $router;
        $this->cacheDir       = $cacheDir;
        $this->bundles        = $bundles;

        $domainPatterns = $this->extractDomainPatterns($routesToExpose);

        $this->availableDomains = array_keys($domainPatterns);

        $this->pattern = $this->buildPattern($domainPatterns);
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutes()
    {
        $collection = $this->router->getRouteCollection();
        $routes     = new RouteCollection();

        /** @var Route $route */
        foreach ($collection->all() as $name => $route) {

            if ($route->hasOption('expose')) {
                $routes->add($name, $route);
                continue;
            }

            preg_match('#^' . $this->pattern . '$#', $name, $matches);

            if (count($matches) === 0) {
                continue;
            }

            $domain = $this->getDomainByRouteMatches($matches, $name);

            if (is_null($domain)) {
                continue;
            }

            $route = clone $route;
            $route->setOption('expose', $domain);
            $routes->add($name, $route);
        }

        return $routes;
    }

    /**
     * {@inheritDoc}
     */
    public function getBaseUrl()
    {
        return $this->router->getContext()->getBaseUrl() ?: '';
    }

    /**
     * {@inheritDoc}
     */
    public function getPrefix($locale)
    {
        if (isset($this->bundles['JMSI18nRoutingBundle'])) {
            return $locale . I18nLoader::ROUTING_PREFIX;
        }

        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        $requestContext = $this->router->getContext();

        $host = $requestContext->getHost() .
            ('' === $this->getPort() ? $this->getPort() : ':' . $this->getPort());

        return $host;
    }

    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        $requestContext = $this->router->getContext();

        $port="";
        if ($this->usesNonStandardPort()) {
            $method = sprintf('get%sPort', ucfirst($requestContext->getScheme()));
            $port = $requestContext->$method();
        }

        return $port;
    }

    /**
     * {@inheritDoc}
     */
    public function getScheme()
    {
        return $this->router->getContext()->getScheme();
    }

    /**
     * {@inheritDoc}
     */
    public function getCachePath($locale)
    {
        $cachePath = $this->cacheDir . DIRECTORY_SEPARATOR . 'fosJsRouting';
        if (!file_exists($cachePath)) {
            mkdir($cachePath);
        }

        if (isset($this->bundles['JMSI18nRoutingBundle'])) {
            $cachePath = $cachePath . DIRECTORY_SEPARATOR . 'data.' . $locale . '.json';
        } else {
            $cachePath = $cachePath . DIRECTORY_SEPARATOR . 'data.json';
        }

        return $cachePath;
    }

    /**
     * {@inheritDoc}
     */
    public function getResources()
    {
        return $this->router->getRouteCollection()->getResources();
    }

    /**
     * {@inheritDoc}
     */
    public function isRouteExposed(Route $route, $name)
    {
        return true === $route->hasOption('expose') ||
            ('' !== $this->pattern && preg_match('#^' . $this->pattern . '$#', $name));
    }

    protected function getDomainByRouteMatches($matches, $name)
    {
        $matches = array_filter($matches, function($match) {
            return !empty($match);
        });

        $matches = array_flip(array_intersect_key($matches, array_flip($this->availableDomains)));

        return isset($matches[$name]) ? $matches[$name] : null;
    }

    protected function extractDomainPatterns($routesToExpose)
    {
        $domainPatterns = array();

        foreach ($routesToExpose as $item) {

            if (is_string($item)) {
                $domainPatterns['default'][] = $item;
                continue;
            }

            if (is_array($item) && is_string($item['pattern'])) {

                if (!isset($item['domain'])) {
                    $domainPatterns['default'][] = $item['pattern'];
                    continue;
                } elseif (is_string($item['domain'])) {
                    $domainPatterns[$item['domain']][] = $item['pattern'];
                    continue;
                }

            }

            throw new \Exception('routes_to_expose definition is invalid');
        }

        return $domainPatterns;
    }

    /**
     * Convert the routesToExpose array in a regular expression pattern
     *
     * @param $domainPatterns
     * @return string
     * @throws \Exception
     */
    protected function buildPattern($domainPatterns)
    {
        $patterns = array();

        foreach ($domainPatterns as $domain => $items) {

            $patterns[] =  '(?P<' . $domain . '>' . implode('|', $items) . ')';
        }

        return implode('|', $patterns);
    }

    /**
     * Check whether server is serving this request from a non-standard port
     *
     * @return bool
     */
    private function usesNonStandardPort()
    {
        return $this->usesNonStandardHttpPort() || $this->usesNonStandardHttpsPort();
    }

    /**
     * Check whether server is serving HTTP over a non-standard port
     *
     * @return bool
     */
    private function usesNonStandardHttpPort()
    {
        return 'http' === $this->getScheme() && '80' != $this->router->getContext()->getHttpPort();
    }

    /**
     * Check whether server is serving HTTPS over a non-standard port
     *
     * @return bool
     */
    private function usesNonStandardHttpsPort()
    {
        return 'https' === $this->getScheme() && '443' != $this->router->getContext()->getHttpsPort();
    }
}
