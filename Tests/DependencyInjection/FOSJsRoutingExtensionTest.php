<?php

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
