<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Tests\Controller;

use FOS\JsRoutingBundle\Controller\Controller;
use FOS\JsRoutingBundle\Serializer\Denormalizer\RouteCollectionDenormalizer;
use FOS\JsRoutingBundle\Serializer\Normalizer\RouteCollectionNormalizer;
use FOS\JsRoutingBundle\Serializer\Normalizer\RoutesResponseNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class ControllerTest extends TestCase
{
    private $cachePath;

    public function setUp()
    {
        $this->cachePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fosJsRouting' . DIRECTORY_SEPARATOR . 'data.json';
    }

    public function tearDown()
    {
        unlink($this->cachePath);
    }

    public function testIndexAction()
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/homepage'));
        $routes->add('blog', new Route('/blog-post/{slug}', array(), array(), array(), 'localhost'));

        $controller = new Controller(
            $this->getSerializer(),
            $this->getExtractor($routes)
        );

        $response = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]}},"prefix":"","host":"","scheme":""}', $response->getContent());
    }

    public function testIndexActionWithLocalizedRoutes()
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/homepage'));
        $routes->add('blog', new Route('/blog-post/{slug}/{_locale}', array(), array(), array(), 'localhost'));

        $controller = new Controller(
            $this->getSerializer(),
            $this->getExtractor($routes)
        );

        $response = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"blog":{"tokens":[["variable","\/","[^\/]++","_locale"],["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":{"_locale":"en"},"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]}},"prefix":"","host":"","scheme":""}', $response->getContent());
    }

    public function testConfigCache()
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/homepage'));

        $controller = new Controller(
            $this->getSerializer(),
            $this->getExtractor($routes)
        );

        $response = $controller->indexAction($this->getRequest('/'), 'json');
        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]}},"prefix":"","host":"","scheme":""}', $response->getContent());

        // second call should serve the cached content
        $response = $controller->indexAction($this->getRequest('/'), 'json');
        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]}},"prefix":"","host":"","scheme":""}', $response->getContent());
    }

    /**
     * @dataProvider dataProviderForTestGenerateWithCallback
     */
    public function testGenerateWithCallback($callback)
    {
        $controller = new Controller($this->getSerializer(), $this->getExtractor());
        $response   = $controller->indexAction($this->getRequest('/', 'GET', array('callback' => $callback)), 'json');

        $this->assertEquals(
            sprintf('/**/%s({"base_url":"","routes":[],"prefix":"","host":"","scheme":""});', $callback),
            $response->getContent()
        );
    }

    public static function dataProviderForTestGenerateWithCallback()
    {
        return array(
            array('fos.Router.data'),
            array('foo'),
        );
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testGenerateWithInvalidCallback()
    {
        $controller = new Controller($this->getSerializer(), $this->getExtractor());
        $controller->indexAction($this->getRequest('/', 'GET', array('callback' => '(function xss(x) {evil()})')), 'json');
    }

    public function testIndexActionWithoutRoutes()
    {
        $controller = new Controller($this->getSerializer(), $this->getExtractor(), array(), sys_get_temp_dir());
        $response   = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertEquals('{"base_url":"","routes":[],"prefix":"","host":"","scheme":""}', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $this->assertFalse($response->headers->hasCacheControlDirective('public'));
        $this->assertNull($response->getExpires());
        $this->assertFalse($response->headers->hasCacheControlDirective('max-age'));
        $this->assertFalse($response->headers->hasCacheControlDirective('s-maxage'));
    }

    public function testCacheControl()
    {
        $cacheControlConfig = array(
            'enabled' => true,
            'public'  => true,
            'expires' => '2013-10-04 23:59:59 UTC',
            'maxage'  => 123,
            'smaxage' => 456,
            'vary'    => array(),
        );

        $controller = new Controller($this->getSerializer(), $this->getExtractor(), $cacheControlConfig, sys_get_temp_dir());
        $response   = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertTrue($response->headers->hasCacheControlDirective('public'));

        $this->assertEquals('2013-10-04 23:59:59', $response->getExpires()->format('Y-m-d H:i:s'));

        $this->assertTrue($response->headers->hasCacheControlDirective('max-age'));
        $this->assertEquals(123, $response->headers->getCacheControlDirective('max-age'));

        $this->assertTrue($response->headers->hasCacheControlDirective('s-maxage'));
        $this->assertEquals(456, $response->headers->getCacheControlDirective('s-maxage'));
    }

    private function getExtractor(RouteCollection $exposedRoutes = null, $baseUrl = '')
    {
        if (null === $exposedRoutes) {
            $exposedRoutes = new RouteCollection();
        }

        $extractor = $this->getMockBuilder('FOS\\JsRoutingBundle\\Extractor\\ExposedRoutesExtractorInterface')->getMock();
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
        $extractor
            ->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue(''))
        ;
        $extractor
            ->expects($this->any())
            ->method('getScheme')
            ->will($this->returnValue(''))
        ;

        return $extractor;
    }

    private function getSerializer()
    {
        if (!class_exists('Symfony\\Component\\Serializer\\Serializer')) {
            $this->markTestSkipped('The Serializer component is not available.');
        }

        return new Serializer(array(
            new RoutesResponseNormalizer(new RouteCollectionNormalizer()),
            new RouteCollectionNormalizer(),
            new RouteCollectionDenormalizer(),
        ), array(
            'json' => new JsonEncoder()
        ));
    }

    private function getRequest($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
    {
        return Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
    }
}
