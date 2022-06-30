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

namespace FOS\JsRoutingBundle\Tests\DependencyInjection;

use FOS\JsRoutingBundle\DependencyInjection\FOSJsRoutingExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FOSJsRoutingExtensionTest extends TestCase
{
    public function setUp(): void
    {
        if (!class_exists('Symfony\Component\DependencyInjection\ContainerBuilder')) {
            $this->markTestSkipped('The DependencyInjection component is not available.');
        }
    }

    public function testRouterAlias(): void
    {
        $container = $this->load([]);

        $this->assertTrue($container->hasAlias('fos_js_routing.router'));
        $alias = $container->getAlias('fos_js_routing.router');

        $this->assertEquals('router', (string) $alias);
        $this->assertFalse($alias->isPublic());
    }

    public function testCustomRouterAlias(): void
    {
        $container = $this->load([['router' => 'demo_router']]);

        $this->assertTrue($container->hasAlias('fos_js_routing.router'));
        $alias = $container->getAlias('fos_js_routing.router');

        $this->assertEquals('demo_router', (string) $alias);
        $this->assertFalse($alias->isPublic());
    }

    public function testLoadSetupsSerializerIfNotGiven(): void
    {
        $container = $this->load([[]]);

        $serializer = $container->get('fos_js_routing.serializer');
        $this->assertEquals('{"foo":"bar"}', $serializer->serialize(['foo' => 'bar'], 'json'));
    }

    private function load(array $configs)
    {
        $container = new ContainerBuilder();

        $extension = new FOSJsRoutingExtension();
        $extension->load($configs, $container);

        return $container;
    }
}
