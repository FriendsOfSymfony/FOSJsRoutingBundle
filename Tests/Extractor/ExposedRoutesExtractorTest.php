<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Tests\Extractor;

use FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractor;
use FOS\JsRoutingBundle\Extractor\ExtractedRoute;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

/**
 * ExposedRoutesExtractorTest class.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class ExposedRoutesExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRoutes()
    {
        $router = $this->getRouter(array(
            'literal' => new Route('/literal'),
            'blog_post' => new Route('/blog-post/{slug}'),
            'list' => new Route('/list/{page}', array('page' => 1), array('page' => '\d+')),
        ));

        $extractor = new ExposedRoutesExtractor($router, array('.*'));
        $this->assertEquals(array(
            'literal' => new ExtractedRoute(array(array('text', '/literal')), array()),
            'blog_post' => new ExtractedRoute(array(array('variable', '/', '[^/]+?', 'slug'), array('text', '/blog-post')), array()),
            'list' => new ExtractedRoute(array(array('variable', '/', '\d+', 'page'), array('text', '/list')), array('page' => 1)),
        ), $extractor->getRoutes());
    }

    public function testGetRoutesWithPatterns()
    {
        $router = $this->getRouter(array(
            // Not exposed
            'hello_you'     => new Route('/foo', array('_controller' => '')),
            'hello_123'     => new Route('/foo', array('_controller' => '')),
            'hello_world'   => new Route('/foo', array('_controller' => '')),
        ));

        $extractor = new ExposedRoutesExtractor($router, array('hello_.*'));
        $this->assertEquals(3, count($extractor->getRoutes()), '3 routes match the pattern: "hello_.*"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_[0-9]{3}'));
        $this->assertEquals(1, count($extractor->getRoutes()), '1 routes match the pattern: "hello_[0-9]{3}"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_[0-9]{4}'));
        $this->assertEquals(0, count($extractor->getRoutes()), '1 routes match the pattern: "hello_[0-9]{4}"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_.+o.+'));
        $this->assertEquals(2, count($extractor->getRoutes()), '2 routes match the pattern: "hello_.+o.+"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_.+o.+', 'hello_123'));
        $this->assertEquals(3, count($extractor->getRoutes()), '3 routes match patterns: "hello_.+o.+" and "hello_123"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_.+o.+', 'hello_$'));
        $this->assertEquals(2, count($extractor->getRoutes()), '2 routes match patterns: "hello_.+o.+" and "hello_"');

        $extractor = new ExposedRoutesExtractor($router, array());
        $this->assertEquals(0, count($extractor->getRoutes()), 'No patterns so no matched routes');
    }

    /**
     * Get a mock object which represents a Router.
     * @return \Symfony\Component\Routing\Router
     */
    private function getRouter(array $routes)
    {
        $routeCollection = $this->getMock('\Symfony\Component\Routing\RouteCollection', array(), array(), '', false);
        $routeCollection
            ->expects($this->atLeastOnce())
            ->method('all')
            ->will($this->returnValue($routes));

        $router = $this->getMock('\Symfony\Component\Routing\Router', array(), array(), '', false);
        $router
            ->expects($this->atLeastOnce())
            ->method('getRouteCollection')
            ->will($this->returnValue($routeCollection));

        return $router;
    }
}
