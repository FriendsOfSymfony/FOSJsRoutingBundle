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
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpKernel\Kernel;

/**
 * ExposedRoutesExtractorTest class.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class ExposedRoutesExtractorTest extends \PHPUnit_Framework_TestCase
{
    private $cacheDir;

    public function setUp()
    {
        if (!class_exists('Symfony\\Component\\Routing\\Route')) {
            $this->markTestSkipped('The Routing component is not available.');
        }

        $this->cacheDir = sys_get_temp_dir();
    }

    public function testGetRoutes()
    {
        $router = $this->getRouter(array(
            'literal' => new Route('/literal'),
            'blog_post' => new Route('/blog-post/{slug}'),
            'list' => new Route('/list/{page}', array('page' => 1), array('page' => '\d+')),
        ));

        if (defined('Symfony\Component\HttpKernel\Kernel::VERSION') && version_compare(Kernel::VERSION, '2.2', '>=')) {
            $expected = array(
                'literal'   => new ExtractedRoute(array(array('text', '/literal')), array(), array()),
                'blog_post' => new ExtractedRoute(array(array('variable', '/', '[^/]++', 'slug'), array('text', '/blog-post')), array(), array()),
                'list'      => new ExtractedRoute(array(array('variable', '/', '\d+', 'page'), array('text', '/list')), array('page' => 1), array('page' => '\d+'))
            );
        } elseif (defined('Symfony\Component\HttpKernel\Kernel::VERSION_ID') && version_compare(Kernel::VERSION_ID, '20100', '>=')) {
            $expected = array(
                'literal'   => new ExtractedRoute(array(array('text', '/literal')), array(), array()),
                'blog_post' => new ExtractedRoute(array(array('variable', '/', '[^/]+', 'slug'), array('text', '/blog-post')), array(), array()),
                'list'      => new ExtractedRoute(array(array('variable', '/', '\d+', 'page'), array('text', '/list')), array('page' => 1), array('page' => '\d+'))
            );
        } else {
            $expected = array(
                'literal'   => new ExtractedRoute(array(array('text', '/literal')), array(), array()),
                'blog_post' => new ExtractedRoute(array(array('variable', '/', '[^/]+?', 'slug'), array('text', '/blog-post')), array(), array()),
                'list'      => new ExtractedRoute(array(array('variable', '/', '\d+', 'page'), array('text', '/list')), array('page' => 1), array('page' => '\d+'))
            );
        }

        $extractor = new ExposedRoutesExtractor($router, array('.*'), $this->cacheDir, array());
        $this->assertEquals($expected, $extractor->getRoutes());
    }

    public function testGetRoutesWithPatterns()
    {
        $router = $this->getRouter(array(
            // Not exposed
            'hello_you'     => new Route('/foo', array('_controller' => '')),
            'hello_123'     => new Route('/foo', array('_controller' => '')),
            'hello_world'   => new Route('/foo', array('_controller' => '')),
        ));

        $extractor = new ExposedRoutesExtractor($router, array('hello_.*'), $this->cacheDir, array());
        $this->assertEquals(3, count($extractor->getRoutes()), '3 routes match the pattern: "hello_.*"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_[0-9]{3}'), $this->cacheDir, array());
        $this->assertEquals(1, count($extractor->getRoutes()), '1 routes match the pattern: "hello_[0-9]{3}"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_[0-9]{4}'), $this->cacheDir, array());
        $this->assertEquals(0, count($extractor->getRoutes()), '1 routes match the pattern: "hello_[0-9]{4}"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_.+o.+'), $this->cacheDir, array());
        $this->assertEquals(2, count($extractor->getRoutes()), '2 routes match the pattern: "hello_.+o.+"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_.+o.+', 'hello_123'), $this->cacheDir, array());
        $this->assertEquals(3, count($extractor->getRoutes()), '3 routes match patterns: "hello_.+o.+" and "hello_123"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_.+o.+', 'hello_$'), $this->cacheDir, array());
        $this->assertEquals(2, count($extractor->getRoutes()), '2 routes match patterns: "hello_.+o.+" and "hello_"');

        $extractor = new ExposedRoutesExtractor($router, array(), $this->cacheDir, array());
        $this->assertEquals(0, count($extractor->getRoutes()), 'No patterns so no matched routes');
    }

    public function testGetCachePath()
    {
        $router = $this->getMock('Symfony\\Component\\Routing\\Router', array(), array(), '', false);

        $extractor = new ExposedRoutesExtractor($router, array(), $this->cacheDir, array());
        $this->assertEquals($this->cacheDir . DIRECTORY_SEPARATOR . 'fosJsRouting' . DIRECTORY_SEPARATOR . 'data.json', $extractor->getCachePath(''));
    }

    /**
     * @dataProvider provideTestGetHostOverHttp
     */
    public function testGetHostOverHttp($host, $httpPort, $expected)
    {
        $requestContext = new RequestContext('/app_dev.php', 'GET', $host, 'http', $httpPort);

        $router = $this->getMock('Symfony\\Component\\Routing\\Router', array(), array(), '', false);
        $router->expects($this->atLeastOnce())
            ->method('getContext')
            ->will($this->returnValue($requestContext));

        $extractor = new ExposedRoutesExtractor($router, array(), $this->cacheDir, array());

        $this->assertEquals($expected, $extractor->getHost());
    }
    public function provideTestGetHostOverHttp()
    {
        return array(
            'HTTP Standard' => array('127.0.0.1', 80, '127.0.0.1'),
            'HTTP Non-Standard' => array('127.0.0.1', 8888, '127.0.0.1:8888'),
        );
    }

    /**
     * @dataProvider provideTestGetHostOverHttps
     */
    public function testGetHostOverHttps($host, $httpsPort, $expected)
    {
        $requestContext = new RequestContext('/app_dev.php', 'GET', $host, 'https', 80, $httpsPort);

        $router = $this->getMock('Symfony\\Component\\Routing\\Router', array(), array(), '', false);
        $router->expects($this->atLeastOnce())
            ->method('getContext')
            ->will($this->returnValue($requestContext));

        $extractor = new ExposedRoutesExtractor($router, array(), $this->cacheDir, array());

        $this->assertEquals($expected, $extractor->getHost());
    }
    public function provideTestGetHostOverHttps()
    {
        return array(
            'HTTPS Standard' => array('127.0.0.1', 443, '127.0.0.1'),
            'HTTPS Non-Standard' => array('127.0.0.1', 9876, '127.0.0.1:9876'),
        );
    }

    /**
     * Get a mock object which represents a Router.
     * @return \Symfony\Component\Routing\Router
     */
    private function getRouter(array $routes)
    {
        $routeCollection = $this->getMock('Symfony\\Component\\Routing\\RouteCollection', array(), array(), '', false);
        $routeCollection
            ->expects($this->atLeastOnce())
            ->method('all')
            ->will($this->returnValue($routes));

        $router = $this->getMock('Symfony\\Component\\Routing\\Router', array(), array(), '', false);
        $router
            ->expects($this->atLeastOnce())
            ->method('getRouteCollection')
            ->will($this->returnValue($routeCollection));

        return $router;
    }
}
