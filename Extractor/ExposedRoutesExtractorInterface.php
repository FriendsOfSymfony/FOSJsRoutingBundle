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

use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\Routing\Route;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
interface ExposedRoutesExtractorInterface
{
    /**
     * Returns a collection of exposed routes
     *
     * @param array $sets
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes(array $sets = array());

    /**
     * Return the Base URL
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * Get the route prefix to use, i.e. the language if JMSI18nRoutingBundle is active
     *
     * @param string $locale the request locale
     *
     * @return string
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
     * @param string $locale the request locale
     *
     * @return string
     */
    public function getCachePath($locale, array $sets = array());

    /**
     * Return an array of routing resources
     *
     * @return ResourceInterface[]
     */
    public function getResources();

    /**
     * Tell whether a route should be considered as exposed. If $sets in not
     * empty, it will also test if one of the route exposet_sets is present in
     * the $sets argument.
     *
     * @param Route  $route
     * @param string $name
     *
     * @return bool
     */
    public function isRouteExposed(Route $route, $name, array $sets = array());
}
