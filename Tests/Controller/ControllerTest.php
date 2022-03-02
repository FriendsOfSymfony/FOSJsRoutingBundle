<?php

declare(strict_types=1);

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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class ControllerTest extends TestCase
{
    private $cachePath;

    public function setUp(): void
    {
        $this->cachePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'fosJsRouting'.DIRECTORY_SEPARATOR.'data.json';
    }

    public function tearDown(): void
    {
        unlink($this->cachePath);
    }

    public function testIndexAction(): void
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/homepage'));
        $routes->add('blog', new Route('/blog-post/{slug}', [], [], [], 'localhost'));

        $controller = new Controller(
            $this->getSerializer(),
            $this->getExtractor($routes)
        );

        $response = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());
    }

    public function testIndexWithExplicitTokenAction(): void
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/homepage'));
        $routes->add('blog', new Route('/blog-post/{!slug}', [], [], [], 'localhost'));

        $controller = new Controller(
            $this->getSerializer(),
            $this->getExtractor($routes)
        );

        $response = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug",false,true],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());
    }

    public function testIndexActionWithLocalizedRoutes(): void
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/homepage'));
        $routes->add('blog', new Route('/blog-post/{slug}/{_locale}', [], [], [], 'localhost'));

        $controller = new Controller(
            $this->getSerializer(),
            $this->getExtractor($routes)
        );

        $response = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"blog":{"tokens":[["variable","\/","[^\/]++","_locale"],["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":{"_locale":"en"},"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());
    }

    public function testConfigCache(): void
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/homepage'));

        $controller = new Controller(
            $this->getSerializer(),
            $this->getExtractor($routes)
        );

        $response = $controller->indexAction($this->getRequest('/'), 'json');
        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());

        // second call should serve the cached content
        $response = $controller->indexAction($this->getRequest('/'), 'json');
        $this->assertEquals('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());
    }

    /**
     * @dataProvider dataProviderForTestGenerateWithCallback
     */
    public function testGenerateWithCallback($callback): void
    {
        $controller = new Controller($this->getSerializer(), $this->getExtractor());
        $response = $controller->indexAction($this->getRequest('/', 'GET', ['callback' => $callback]), 'json');

        $this->assertEquals(
            sprintf('/**/%s({"base_url":"","routes":[],"prefix":"","host":"","port":null,"scheme":"","locale":"en"});', $callback),
            $response->getContent()
        );
    }

    public static function dataProviderForTestGenerateWithCallback()
    {
        return [
            ['fos.Router.data'],
            ['foo'],
        ];
    }

    public function testGenerateWithInvalidCallback(): void
    {
        $this->expectException(HttpException::class);
        $controller = new Controller($this->getSerializer(), $this->getExtractor());
        $controller->indexAction($this->getRequest('/', 'GET', ['callback' => '(function xss(x) {evil()})']), 'json');
    }

    public function testIndexActionWithoutRoutes(): void
    {
        $controller = new Controller($this->getSerializer(), $this->getExtractor());
        $response = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertEquals('{"base_url":"","routes":[],"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $this->assertFalse($response->headers->hasCacheControlDirective('public'));
        $this->assertNull($response->getExpires());
        $this->assertFalse($response->headers->hasCacheControlDirective('max-age'));
        $this->assertFalse($response->headers->hasCacheControlDirective('s-maxage'));
    }

    public function testCacheControl(): void
    {
        $cacheControlConfig = [
            'enabled' => true,
            'public' => true,
            'expires' => '2013-10-04 23:59:59 UTC',
            'maxage' => 123,
            'smaxage' => 456,
            'vary' => [],
        ];

        $controller = new Controller($this->getSerializer(), $this->getExtractor(), $cacheControlConfig);
        $response = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertTrue($response->headers->hasCacheControlDirective('public'));

        $this->assertEquals('2013-10-04 23:59:59', $response->getExpires()->format('Y-m-d H:i:s'));

        $this->assertTrue($response->headers->hasCacheControlDirective('max-age'));
        $this->assertEquals(123, $response->headers->getCacheControlDirective('max-age'));

        $this->assertTrue($response->headers->hasCacheControlDirective('s-maxage'));
        $this->assertEquals(456, $response->headers->getCacheControlDirective('s-maxage'));
    }

    public function testExposeDomain(): void
    {
        $routes = new RouteCollection();
        $routes->add('homepage', new Route('/'));
        $routes->add('admin_index', new Route('/admin', [], [],
            ['expose' => 'admin']));
        $routes->add('admin_pages', new Route('/admin/path', [], [],
            ['expose' => 'admin']));
        $routes->add('blog_index', new Route('/blog', [], [],
            ['expose' => 'blog'], 'localhost'));
        $routes->add('blog_post', new Route('/blog/{slug}', [], [],
            ['expose' => 'blog'], 'localhost'));

        $controller = new Controller(
            $this->getSerializer(),
            $this->getExtractor($routes)
        );

        $response = $controller->indexAction($this->getRequest('/'), 'json');

        $this->assertEquals('{"base_url":"","routes":{"homepage":{"tokens":[["text","\/"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());

        $response = $controller->indexAction($this->getRequest('/',
            'GET', ['domain' => 'admin']), 'json');

        $this->assertEquals('{"base_url":"","routes":{"admin_index":{"tokens":[["text","\/admin"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"admin_pages":{"tokens":[["text","\/admin\/path"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());

        $response = $controller->indexAction($this->getRequest('/',
            'GET', ['domain' => 'blog']), 'json');

        $this->assertEquals('{"base_url":"","routes":{"blog_index":{"tokens":[["text","\/blog"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]},"blog_post":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());

        $response = $controller->indexAction($this->getRequest('/',
            'GET', ['domain' => 'admin,blog']), 'json');

        $this->assertEquals('{"base_url":"","routes":{"admin_index":{"tokens":[["text","\/admin"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"admin_pages":{"tokens":[["text","\/admin\/path"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"blog_index":{"tokens":[["text","\/blog"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]},"blog_post":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());

        $response = $controller->indexAction($this->getRequest('/',
            'GET', ['domain' => 'default,admin,blog']), 'json');

        $this->assertEquals('{"base_url":"","routes":{"homepage":{"tokens":[["text","\/"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"admin_index":{"tokens":[["text","\/admin"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"admin_pages":{"tokens":[["text","\/admin\/path"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"blog_index":{"tokens":[["text","\/blog"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]},"blog_post":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]],"methods":[],"schemes":[]}},"prefix":"","host":"","port":null,"scheme":"","locale":"en"}', $response->getContent());
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

        return new Serializer([
            new RoutesResponseNormalizer(new RouteCollectionNormalizer()),
            new RouteCollectionNormalizer(),
            new RouteCollectionDenormalizer(),
        ], [
            'json' => new JsonEncoder(),
        ]);
    }

    private function getRequest($uri, $method = 'GET', $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        return Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
    }
}
