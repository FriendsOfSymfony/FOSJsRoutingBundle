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

use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
interface ExposedRoutesExtractorInterface
{
    /**
     * Returns a collection of exposed routes.
     */
    public function getRoutes(): RouteCollection;

    /**
     * Return the Base URL.
     */
    public function getBaseUrl(): string;

    /**
     * Get the route prefix to use, i.e. the language if JMSI18nRoutingBundle is active.
     */
    public function getPrefix(string $locale): string;

    /**
     * Get the host and applicable port from RequestContext.
     */
    public function getHost(): string;

    /**
     * Get the port from RequestContext, only if non standard port (Eg: "8080").
     */
    public function getPort(): ?string;

    /**
     * Get the scheme from RequestContext.
     */
    public function getScheme(): string;

    /**
     * Get the cache path for this request.
     *
     * @param string|null $locale the request locale
     */
    public function getCachePath(?string $locale): string;

    /**
     * Return an array of routing resources.
     *
     * @return ResourceInterface[]
     */
    public function getResources(): array;

    /**
     * Tell whether a route should be considered as exposed.
     */
    public function isRouteExposed(Route $route, string $name): bool;
}
