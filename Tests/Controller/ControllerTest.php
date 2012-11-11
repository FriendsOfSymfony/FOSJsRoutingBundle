<?php

namespace FOS\JsRoutingBundle\Tests\Controller;

use FOS\JsRoutingBundle\Controller\Controller;
use FOS\JsRoutingBundle\Extractor\ExtractedRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    private $cachePath;

    public function setUp()
    {
        $this->cachePath = sys_get_temp_dir() . '/fosJsRouting/data.json';
    }

    public function tearDown()
    {
        unlink($this->cachePath);
    }

    public function testIndexAction()
    {
        $controller = new Controller(
            $this->getSerializer(),
            $this->getExtractor(array(
                'literal' => new ExtractedRoute(array(array('text', '/homepage')), array()),
                'blog'    => new ExtractedRoute(array(array('variable', '/', '[^/]+?', 'slug'), array('text', '/blog-post')), array()),
            ))
        );

        $response = $controller->indexAction(Request::create('/'), 'json');
        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[]},"blog":{"tokens":[["variable","\/","[^\/]+?","slug"],["text","\/blog-post"]],"defaults":[]}},"prefix":""}', $response->getContent());
    }

    public function testGenerateWithCallback()
    {
        $controller = new Controller($this->getSerializer(), $this->getExtractor());

        $response = $controller->indexAction(Request::create('/', 'GET', array('callback' => 'foo')), 'json');
        $this->assertEquals('foo({"base_url":"","routes":[],"prefix":""});', $response->getContent());
    }

    public function testIndexActionWithoutRoutes()
    {
        $controller = new Controller($this->getSerializer(), $this->getExtractor(), sys_get_temp_dir(), array());

        $response = $controller->indexAction(Request::create('/'), 'json');
        $this->assertEquals('{"base_url":"","routes":[],"prefix":""}', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    private function getExtractor(array $exposedRoutes = array(), $baseUrl = '')
    {
        $extractor = $this->getMock('FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface');
        $extractor
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue($exposedRoutes))
        ;
        $extractor
            ->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue($baseUrl))
        ;
        $extractor
            ->expects($this->any())
            ->method('getCachePath')
            ->will($this->returnValue($this->cachePath))
        ;
        $extractor
            ->expects($this->any())
            ->method('getPrefix')
            ->will($this->returnValue(''))
        ;

        return $extractor;
    }

    private function getSerializer()
    {
        if (!class_exists('Symfony\Component\Serializer\Serializer')) {
            $this->markTestSkipped('The Serializer component is not available.');
        }

        return new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
    }
}