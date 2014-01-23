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
 * ExposedRoutesExtractorInterface interface.
 *
 * @author      William DURAND <william.durand1@gmail.com>
 */
interface ExposedRoutesExtractorInterface
{
    /**
     * Returns an array of exposed routes where keys are the route names.
     *
     * @return ExtractedRoute[]
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
    public function getCachePath($locale);

    /**
     * Returns an array of routing resources.
     *
     * @return ResourceInterface[]
     */
    public function getResources();

    /**
     * Returns an array of all exposed Route objects.
     *
     * @return Route[]
     */
    public function getExposedRoutes();
}
