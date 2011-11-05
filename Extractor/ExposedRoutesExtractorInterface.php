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
     * @return array
     */
    function getRoutes();

    /**
     * Returns the Base URL.
     *
     * @return string
     */
    function getBaseUrl();

    /**
     * Returns an array of routing resources.
     *
     * @return array
     */
    function getResources();

    /**
     * Returns an array of all exposed Route objects.
     *
     * @return array
     */
    function getExposedRoutes();
}
