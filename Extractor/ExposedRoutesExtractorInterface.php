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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * ExposedRoutesExtractorInterface interface.
 *
 * @author      William DURAND <william.durand1@gmail.com>
 */
interface ExposedRoutesExtractorInterface
{
    /**
     * Returns an array of exposed routes where keys are the route names.
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes();

    /**
     * Returns the Base URL.
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * Get the route prefix to use, i.e. the language if JMSI18nRoutingBundle is active
     *
     * @var string $locale the request locale
     */
    public function getPrefix($locale);

    /**
     * Get the host and applicable port from RequestContext
     *
     * @return string
     */
    public function getHost();

    /**
     * Get the scheme from RequestContext
     *
     * @return string
     */
    public function getScheme();

    /**
     * Get the cache path for this request
     *
     * @var string $locale the request locale
     */
    public function getCachePath($locale);

    /**
     * Returns an array of routing resources.
     *
     * @return array
     */
    public function getResources();

    /**
     * Tells if a route should be considered as exposed
     *
     * @param Route  $route
     * @param string $name
     *
     * @return bool
     */
    public function isRouteExposed(Route $route, $name);
}
