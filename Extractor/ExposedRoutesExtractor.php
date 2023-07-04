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

namespace FOS\JsRoutingBundle\Extractor;

use JMS\I18nRoutingBundle\Router\I18nLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author      William DURAND <william.durand1@gmail.com>
 */
class ExposedRoutesExtractor implements ExposedRoutesExtractorInterface
{
    protected string $pattern;

    protected array $availableDomains;

    /**
     * Default constructor.
     *
     * @param array $routesToExpose some route names to expose
     * @param array $bundles        list of loaded bundles to check when generating the prefix
     *
     * @throws \Exception
     */
    public function __construct(private RouterInterface $router, array $routesToExpose, private string $cacheDir, private array $bundles = [])
    {
        $domainPatterns = $this->extractDomainPatterns($routesToExpose);

        $this->availableDomains = array_keys($domainPatterns);

        $this->pattern = $this->buildPattern($domainPatterns);
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutes(): RouteCollection
    {
        $collection = $this->router->getRouteCollection();
        $routes = new RouteCollection();

        /** @var Route $route */
        foreach ($collection->all() as $name => $route) {
            if ($route->hasOption('expose')) {
                $expose = $route->getOption('expose');

                if (false !== $expose && 'false' !== $expose) {
                    $routes->add($name, $route);
                }
                continue;
            }

            preg_match('#^'.$this->pattern.'$#', $name, $matches);

            if (0 === count($matches)) {
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
    public function getBaseUrl(): string
    {
        return $this->router->getContext()->getBaseUrl() ?: '';
    }

    /**
     * {@inheritDoc}
     */
    public function getPrefix(string $locale): string
    {
        if (isset($this->bundles['JMSI18nRoutingBundle'])) {
            return $locale.I18nLoader::ROUTING_PREFIX;
        }

        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getHost(): string
    {
        $requestContext = $this->router->getContext();

        $host = $requestContext->getHost().
            ('' === $this->getPort() ? $this->getPort() : ':'.$this->getPort());

        return $host;
    }

    /**
     * {@inheritDoc}
     */
    public function getPort(): ?string
    {
        $requestContext = $this->router->getContext();

        $port = '';
        if ($this->usesNonStandardPort()) {
            $method = sprintf('get%sPort', ucfirst($requestContext->getScheme()));
            $port = (string) $requestContext->$method();
        }

        return $port;
    }

    /**
     * {@inheritDoc}
     */
    public function getScheme(): string
    {
        return $this->router->getContext()->getScheme();
    }

    /**
     * {@inheritDoc}
     */
    public function getCachePath(string $locale = null): string
    {
        $cachePath = $this->cacheDir.DIRECTORY_SEPARATOR.'fosJsRouting';
        if (!file_exists($cachePath)) {
            if (false === @mkdir($cachePath)) {
                throw new \RuntimeException('Unable to create Cache directory ' . $cachePath);
            }
        }

        if (isset($this->bundles['JMSI18nRoutingBundle'])) {
            $cachePath = $cachePath.DIRECTORY_SEPARATOR.'data.'.$locale.'.json';
        } else {
            $cachePath = $cachePath.DIRECTORY_SEPARATOR.'data.json';
        }

        return $cachePath;
    }

    /**
     * {@inheritDoc}
     */
    public function getResources(): array
    {
        return $this->router->getRouteCollection()->getResources();
    }

    /**
     * {@inheritDoc}
     */
    public function isRouteExposed(Route $route, $name): bool
    {
        if (false === $route->hasOption('expose')) {
            return '' !== $this->pattern && preg_match('#^'.$this->pattern.'$#', $name);
        }

        $status = $route->getOption('expose');

        return false !== $status && 'false' !== $status;
    }

    protected function getDomainByRouteMatches($matches, $name): int|string|null
    {
        $matches = array_filter($matches, fn ($match) => !empty($match));

        $matches = array_flip(array_intersect_key($matches, array_flip($this->availableDomains)));

        return $matches[$name] ?? null;
    }

    protected function extractDomainPatterns($routesToExpose): array
    {
        $domainPatterns = [];

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
     * Convert the routesToExpose array in a regular expression pattern.
     *
     * @throws \Exception
     */
    protected function buildPattern(array $domainPatterns): string
    {
        $patterns = [];

        foreach ($domainPatterns as $domain => $items) {
            $patterns[] = '(?P<'.$domain.'>'.implode('|', $items).')';
        }

        return implode('|', $patterns);
    }

    /**
     * Check whether server is serving this request from a non-standard port.
     */
    private function usesNonStandardPort(): bool
    {
        return $this->usesNonStandardHttpPort() || $this->usesNonStandardHttpsPort();
    }

    /**
     * Check whether server is serving HTTP over a non-standard port.
     */
    private function usesNonStandardHttpPort(): bool
    {
        return 'http' === $this->getScheme() && '80' != $this->router->getContext()->getHttpPort();
    }

    /**
     * Check whether server is serving HTTPS over a non-standard port.
     */
    private function usesNonStandardHttpsPort(): bool
    {
        return 'https' === $this->getScheme() && '443' != $this->router->getContext()->getHttpsPort();
    }
}
