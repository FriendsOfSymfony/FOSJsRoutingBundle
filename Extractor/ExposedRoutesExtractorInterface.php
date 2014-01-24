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
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes();

    /**
     * Get the cache path for this request
     *
     * @param string $locale the request locale
     *
     * @return string
     */
    public function getCachePath($locale);

    /**
     * Return an array of routing resources
     *
     * @return ResourceInterface[]
     */
    public function getResources();

    /**
     * Tell whether a route should be considered as exposed
     *
     * @param Route  $route
     * @param string $name
     *
     * @return bool
     */
    public function isRouteExposed(Route $route, $name);
}
