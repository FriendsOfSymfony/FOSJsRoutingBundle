<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Tests\Command;

use FOS\JsRoutingBundle\Command\RouterDebugExposedCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouterDebugExposedCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $extractor;
    protected $router;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->extractor = $this->getMockBuilder('FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractor')
            ->disableOriginalConstructor()
            ->getMock();

        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        if (!class_exists('Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper')) {
            $this->markTestSkipped('2.3 BC is not tested');
        }

        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal'));
        $routes->add('blog_post', new Route('/blog-post/{slug}'));
        $routes->add('list', new Route('/literal'));

        $this->container->expects($this->once())
            ->method('get')
            ->with('fos_js_routing.extractor')
            ->will($this->returnValue($this->extractor));

        $this->extractor->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));

        $command = new RouterDebugExposedCommand();
        $command->setContainer($this->container);

        $tester = new CommandTester($command);
        $tester->execute(array());

        $this->assertContains('literal   ANY    ANY    ANY  /literal', $tester->getDisplay());
        $this->assertContains('blog_post ANY    ANY    ANY  /blog-post/{slug}', $tester->getDisplay());
        $this->assertContains('list      ANY    ANY    ANY  /literal', $tester->getDisplay());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The route "foobar" does not exist.
     */
    public function testExecuteWithNameUnknown()
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal'));
        $routes->add('blog_post', new Route('/blog-post/{slug}'));
        $routes->add('list', new Route('/literal'));

        $this->router->expects($this->once())
            ->method('getRouteCollection')
            ->will($this->returnValue($routes));

        $this->container->expects($this->at(0))
            ->method('get')
            ->with('fos_js_routing.extractor')
            ->will($this->returnValue($this->extractor));

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('router')
            ->will($this->returnValue($this->router));

        $command = new RouterDebugExposedCommand();
        $command->setContainer($this->container);

        $tester = new CommandTester($command);
        $tester->execute(array('name' => 'foobar'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The route "literal" was found, but it is not an exposed route.
     */
    public function testExecuteWithNameNotExposed()
    {
        if (!class_exists('Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper')) {
            $this->markTestSkipped('2.3 BC is not tested');
        }

        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal'));
        $routes->add('blog_post', new Route('/blog-post/{slug}'));
        $routes->add('list', new Route('/literal'));

        $this->router->expects($this->once())
            ->method('getRouteCollection')
            ->will($this->returnValue($routes));

        $this->container->expects($this->at(0))
            ->method('get')
            ->with('fos_js_routing.extractor')
            ->will($this->returnValue($this->extractor));

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('router')
            ->will($this->returnValue($this->router));

        $command = new RouterDebugExposedCommand();
        $command->setContainer($this->container);

        $tester = new CommandTester($command);
        $tester->execute(array('name' => 'literal'));
    }

    public function testExecuteWithName()
    {
        if (!class_exists('Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper')) {
            $this->markTestSkipped('2.3 BC is not tested');
        }

        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal', array(), array(), array('exposed' => true)));
        $routes->add('blog_post', new Route('/blog-post/{slug}'));
        $routes->add('list', new Route('/literal'));

        $this->router->expects($this->once())
            ->method('getRouteCollection')
            ->will($this->returnValue($routes));

        $this->extractor->expects($this->once())
            ->method('isRouteExposed')
            ->will($this->returnValue(true));

        $this->container->expects($this->at(0))
            ->method('get')
            ->with('fos_js_routing.extractor')
            ->will($this->returnValue($this->extractor));

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('router')
            ->will($this->returnValue($this->router));

        $command = new RouterDebugExposedCommand();
        $command->setContainer($this->container);

        $tester = new CommandTester($command);
        $tester->execute(array('name' => 'literal'));

        $this->assertContains('exposed: true', $tester->getDisplay());
    }
}
