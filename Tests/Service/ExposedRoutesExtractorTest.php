<?php

namespace Bazinga\ExposeRoutingBundle\Tests\Controller;

use Bazinga\ExposeRoutingBundle\Service\ExposedRoutesExtractor;

/**
 * ExposedRoutesExtractorTest class.
 *
 * @package     ExposeRoutingBundle
 * @subpackage  Service
 * @author William DURAND <william.durand1@gmail.com>
 */
class ExposedRoutesExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRoutesWithPatterns()
    {
        $router = $this->getMockRouter();

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
     * Get a mock object which represents a RouteCollection.
     * @return \Symfony\Symfony\Component\Routing\RouteCollection
     */
    private function getMockRouteCollectionNotExposed()
    {
        $array = array(
            // Not exposed
            'hello_you'     => new \Symfony\Component\Routing\Route('/foo', array('_controller' => '')),
            'hello_123'     => new \Symfony\Component\Routing\Route('/foo', array('_controller' => '')),
            'hello_world'   => new \Symfony\Component\Routing\Route('/foo', array('_controller' => '')),
        );

        $routeCollection = $this->getMock('\Symfony\Component\Routing\RouteCollection', array(), array(), '', false);
        $routeCollection
            ->expects($this->atLeastOnce())
            ->method('all')
            ->will($this->returnValue($array));

        return $routeCollection;
    }

    /**
     * Get a mock object which represents a Router.
     * @return \Symfony\Component\Routing\Router
     */
    private function getMockRouter()
    {
        $router = $this->getMock('\Symfony\Component\Routing\Router', array(), array(), '', false);
        $router
            ->expects($this->atLeastOnce())
            ->method('getRouteCollection')
            ->will($this->returnValue($this->getMockRouteCollectionNotExposed()));

        return $router;
    }
}
