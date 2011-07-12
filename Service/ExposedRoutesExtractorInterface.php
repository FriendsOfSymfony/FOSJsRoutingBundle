<?php

namespace Bazinga\ExposeRoutingBundle\Service;

/**
 * ExposedRoutesExtractorInterface interface.
 *
 * @package     ExposeRoutingBundle
 * @subpackage  Service
 * @author William DURAND <william.durand1@gmail.com>
 */
interface ExposedRoutesExtractorInterface
{
    /**
     * Returns an array of exposed routes where keys are the route names.
     * @return array
     */
    function getRoutes();
    /**
     * Returns the Base URL.
     * @return string
     */
    function getBaseUrl();
}
