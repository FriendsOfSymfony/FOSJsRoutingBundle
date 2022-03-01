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

    public function testExecute(): void
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
        $tester->execute([]);

        $this->assertMatchesRegularExpression('/literal(.*ANY){3}.*\/literal/', $tester->getDisplay());
        $this->assertMatchesRegularExpression('/blog_post(.*ANY){3}.*\/blog-post\/{slug}/', $tester->getDisplay());
        $this->assertMatchesRegularExpression('/list(.*ANY){3}.*\/literal/', $tester->getDisplay());
    }

    public function testExecuteWithNameUnknown(): void
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
        $tester->execute(['name' => 'foobar']);
    }

    public function testExecuteWithNameNotExposed(): void
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
        $tester->execute(['name' => 'literal']);
    }

    public function testExecuteWithName(): void
    {
        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal', [], [], ['exposed' => true]));
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
        $tester->execute(['name' => 'literal']);

        $this->assertStringContainsString('exposed: true', $tester->getDisplay());
    }
}
