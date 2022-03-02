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

namespace FOS\JsRoutingBundle\Response;

use Symfony\Component\Routing\RouteCollection;

class RoutesResponse
{
    private $routes;

    public function __construct(private string $baseUrl, RouteCollection $routes = null,
                                private ?string $prefix = null, private ?string $host = null,
                                private ?string $port = null, private ?string $scheme = null,
                                private ?string $locale = null, private array $domains = [])
    {
        $this->routes = $routes ?: new RouteCollection();
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getRoutes(): array
    {
        $exposedRoutes = [];
        foreach ($this->routes->all() as $name => $route) {
            if (!$route->hasOption('expose')) {
                $domain = 'default';
            } else {
                $domain = $route->getOption('expose');
                $domain = is_string($domain) ? ('true' === $domain ? 'default' : $domain) : 'default';
            }

            if (0 === count($this->domains)) {
                if ('default' !== $domain) {
                    continue;
                }
            } elseif (!in_array($domain, $this->domains, true)) {
                continue;
            }

            $compiledRoute = $route->compile();
            $defaults = array_intersect_key(
                $route->getDefaults(),
                array_fill_keys($compiledRoute->getVariables(), null)
            );

            if (!isset($defaults['_locale']) && in_array('_locale', $compiledRoute->getVariables())) {
                $defaults['_locale'] = $this->locale;
            }

            $exposedRoutes[$name] = [
                'tokens' => $compiledRoute->getTokens(),
                'defaults' => $defaults,
                'requirements' => $route->getRequirements(),
                'hosttokens' => method_exists($compiledRoute, 'getHostTokens') ? $compiledRoute->getHostTokens() : [],
                'methods' => $route->getMethods(),
                'schemes' => $route->getSchemes(),
            ];
        }

        return $exposedRoutes;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getPort(): ?string
    {
        return $this->port;
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }
}
