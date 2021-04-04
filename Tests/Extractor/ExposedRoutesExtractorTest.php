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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * ExposedRoutesExtractorTest class.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class ExposedRoutesExtractorTest extends TestCase
{
    private $cacheDir;

    public function setUp(): void
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

        $expected->addOptions(array('expose' => 'default'));

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
        $this->assertCount(3, $extractor->getRoutes(), '3 routes match the pattern: "hello_.*"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_[0-9]{3}'), $this->cacheDir, array());
        $this->assertCount(1, $extractor->getRoutes(), '1 routes match the pattern: "hello_[0-9]{3}"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_[0-9]{4}'), $this->cacheDir, array());
        $this->assertCount(0, $extractor->getRoutes(), '1 routes match the pattern: "hello_[0-9]{4}"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_.+o.+'), $this->cacheDir, array());
        $this->assertCount(2, $extractor->getRoutes(), '2 routes match the pattern: "hello_.+o.+"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_.+o.+', 'hello_123'), $this->cacheDir, array());
        $this->assertCount(3, $extractor->getRoutes(), '3 routes match patterns: "hello_.+o.+" and "hello_123"');

        $extractor = new ExposedRoutesExtractor($router, array('hello_.+o.+', 'hello_$'), $this->cacheDir, array());
        $this->assertCount(2, $extractor->getRoutes(), '2 routes match patterns: "hello_.+o.+" and "hello_"');

        $extractor = new ExposedRoutesExtractor($router, array(), $this->cacheDir, array());
        $this->assertCount(0, $extractor->getRoutes(), 'No patterns so no matched routes');
    }

    public function testGetCachePath()
    {
        $router = $this->getMockBuilder('Symfony\\Component\\Routing\\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $extractor = new ExposedRoutesExtractor($router, array(), $this->cacheDir, array());
        $this->assertEquals($this->cacheDir . DIRECTORY_SEPARATOR . 'fosJsRouting' . DIRECTORY_SEPARATOR . 'data.json', $extractor->getCachePath(''));
    }

    /**
     * @dataProvider provideTestGetHostOverHttp
     */
    public function testGetHostOverHttp($host, $httpPort, $expected)
    {
        $requestContext = new RequestContext('/app_dev.php', 'GET', $host, 'http', $httpPort);

        $router = $this->getMockBuilder('Symfony\\Component\\Routing\\Router')
            ->disableOriginalConstructor()
            ->getMock();
        $router->expects($this->atLeastOnce())
            ->method('getContext')
            ->will($this->returnValue($requestContext));

        $extractor = new ExposedRoutesExtractor($router, array(), $this->cacheDir, array());

        $this->assertEquals($expected, $extractor->getHost());
    }

    /**
     * @return array
     */
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

        $router = $this->getMockBuilder('Symfony\\Component\\Routing\\Router')
            ->disableOriginalConstructor()
            ->getMock();
        $router->expects($this->atLeastOnce())
            ->method('getContext')
            ->will($this->returnValue($requestContext));

        $extractor = new ExposedRoutesExtractor($router, array(), $this->cacheDir, array());

        $this->assertEquals($expected, $extractor->getHost());
    }

    public function testExposeFalse()
    {
        $expected = new RouteCollection();
        $expected->add('user_public_1', new Route('/user/public/1'));
        $expected->add('user_public_2', new Route('/user/public/2', [], [], ['expose' => true]));
        $expected->add('user_public_3', new Route('/user/public/3', [], [], ['expose' => 'true']));
        $expected->add('user_public_4', new Route('/user/public/4', [], [], ['expose' => 'default']));
        $expected->add('user_public_5', new Route('/user/public/5', [], [], ['expose' => 'group_name']));
        $expected->add('user_secret_1', new Route('/user/secret/1', [], [], ['expose' => false]));
        $expected->add('user_secret_2', new Route('/user/secret/2', [], [], ['expose' => 'false']));

        $router = $this->getRouter($expected);
        $extractor = new ExposedRoutesExtractor($router, array('user_.+'), $this->cacheDir, []);

        $this->assertCount(5, $extractor->getRoutes());

        foreach ($expected->all() as $name => $route) {

            if (in_array($name, ['user_secret_1', 'user_secret_2'])) {
                $this->assertFalse($extractor->isRouteExposed($route, $name));
            } else {
                $this->assertTrue($extractor->isRouteExposed($route, $name));
            }

        }
    }

    /**
     * @return array
     */
    public function provideTestGetHostOverHttps()
    {
        return array(
            'HTTPS Standard' => array('127.0.0.1', 443, '127.0.0.1'),
            'HTTPS Non-Standard' => array('127.0.0.1', 9876, '127.0.0.1:9876'),
        );
    }

    /**
     * Get a mock object which represents a Router
     *
     * @return \Symfony\Component\Routing\Router
     */
    private function getRouter(RouteCollection $routes)
    {
        $router = $this->getMockBuilder('Symfony\\Component\\Routing\\Router')
            ->disableOriginalConstructor()
            ->getMock();
        $router
            ->expects($this->atLeastOnce())
            ->method('getRouteCollection')
            ->will($this->returnValue($routes));

        return $router;
    }
}
