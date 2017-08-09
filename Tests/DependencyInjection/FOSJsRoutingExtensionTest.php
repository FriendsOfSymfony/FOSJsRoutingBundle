<?php

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
    public function setUp()
    {
        if (!class_exists('Symfony\Component\DependencyInjection\ContainerBuilder')) {
            $this->markTestSkipped('The DependencyInjection component is not available.');
        }
    }

    public function testRouterAlias()
    {
        $container = $this->load(array());

        $this->assertTrue($container->hasAlias('fos_js_routing.router'));
        $alias = $container->getAlias('fos_js_routing.router');

        $this->assertEquals('router', (string) $alias);
        $this->assertFalse($alias->isPublic());
    }

    public function testCustomRouterAlias()
    {
        $container = $this->load(array(array('router' => 'demo_router')));

        $this->assertTrue($container->hasAlias('fos_js_routing.router'));
        $alias = $container->getAlias('fos_js_routing.router');

        $this->assertEquals('demo_router', (string) $alias);
        $this->assertFalse($alias->isPublic());
    }

    public function testLoadSetupsSerializerIfNotGiven()
    {
        $container = $this->load(array(array()));

        $serializer = $container->get('fos_js_routing.serializer');
        $this->assertEquals('{"foo":"bar"}', $serializer->serialize(array('foo' => 'bar'), 'json'));
    }

    private function load(array $configs)
    {
        $container = new ContainerBuilder();

        $extension = new FOSJsRoutingExtension();
        $extension->load($configs, $container);

        return $container;
    }
}
