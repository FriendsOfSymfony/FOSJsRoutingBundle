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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouterDebugExposedCommandTest extends TestCase
{
    protected $extractor;
    protected $router;

    public function setUp(): void
    {
        $this->extractor = $this->getMockBuilder('FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractor')
            ->disableOriginalConstructor()
            ->getMock();

        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal'));
        $routes->add('blog_post', new Route('/blog-post/{slug}'));
        $routes->add('list', new Route('/literal'));

        $this->extractor->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));

        $command = new RouterDebugExposedCommand($this->extractor, $this->router);

        $tester = new CommandTester($command);
        $tester->execute(array());

        $this->assertRegExp('/literal(.*ANY){3}.*\/literal/', $tester->getDisplay());
        $this->assertRegExp('/blog_post(.*ANY){3}.*\/blog-post\/{slug}/', $tester->getDisplay());
        $this->assertRegExp('/list(.*ANY){3}.*\/literal/', $tester->getDisplay());
    }

    public function testExecuteWithNameUnknown()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The route "foobar" does not exist.');
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal'));
        $routes->add('blog_post', new Route('/blog-post/{slug}'));
        $routes->add('list', new Route('/literal'));

        $this->router->expects($this->once())
            ->method('getRouteCollection')
            ->will($this->returnValue($routes));

        $command = new RouterDebugExposedCommand($this->extractor, $this->router);

        $tester = new CommandTester($command);
        $tester->execute(array('name' => 'foobar'));
    }

    public function testExecuteWithNameNotExposed()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The route "literal" was found, but it is not an exposed route.');
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal'));
        $routes->add('blog_post', new Route('/blog-post/{slug}'));
        $routes->add('list', new Route('/literal'));

        $this->router->expects($this->once())
            ->method('getRouteCollection')
            ->will($this->returnValue($routes));

        $command = new RouterDebugExposedCommand($this->extractor, $this->router);

        $tester = new CommandTester($command);
        $tester->execute(array('name' => 'literal'));
    }

    public function testExecuteWithName()
    {
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

        $command = new RouterDebugExposedCommand($this->extractor, $this->router);

        $tester = new CommandTester($command);
        $tester->execute(array('name' => 'literal'));

        $this->assertStringContainsString('exposed: true', $tester->getDisplay());
    }
}
