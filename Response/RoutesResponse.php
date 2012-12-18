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
    private $host;
    private $scheme;

    public function __construct($baseUrl, array $routes, $prefix, $host, $scheme)
    {
        $this->baseUrl = $baseUrl;
        $this->routes = $routes;
        $this->prefix = $prefix;
        $this->host = $host;
        $this->scheme = $scheme;
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

    public function getHost()
    {
        return $this->host;
    }

    public function getScheme()
    {
        return $this->scheme;
    }
}
