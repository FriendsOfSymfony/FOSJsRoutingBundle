<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Response;

class RoutesResponse
{
    private $baseUrl;
    private $routes;
    private $prefix;

    public function __construct($baseUrl, array $routes, $prefix)
    {
        $this->baseUrl = $baseUrl;
        $this->routes = $routes;
        $this->prefix = $prefix;
    }

    public function getBase_url()
    {
        return $this->baseUrl;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }
}