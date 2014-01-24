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
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

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
        $expected = new RouteCollection();
        $expected->add('literal', new Route('/literal'));
        $expected->add('blog_post', new Route('/blog-post/{slug}'));
        $expected->add('list', new Route('/literal'));

        $router = $this->getRouter($expected);
        $extractor = new ExposedRoutesExtractor($router, array('.*'), $this->cacheDir, array());
        $this->assertEquals($expected, $extractor->getRoutes());
    }

    public function testGetRoutesWithPatterns()
    {
        $expected = new RouteCollection();
        $expected->add('hello_you', new Route('/foo', array('_controller' => '')));
        $expected->add('hello_123', new Route('/foo', array('_controller' => '')));
        $expected->add('hello_world', new Route('/foo', array('_controller' => '')));

        $router = $this->getRouter($expected);

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
     * Get a mock object which represents a Router
     *
     * @return \Symfony\Component\Routing\Router
     */
    private function getRouter(RouteCollection $routes)
    {
        $router = $this->getMock('Symfony\\Component\\Routing\\Router', array(), array(), '', false);
        $router
            ->expects($this->atLeastOnce())
            ->method('getRouteCollection')
            ->will($this->returnValue($routes));

        return $router;
    }
}
