<?php
namespace FOS\JsRoutingBundle\Tests\DependencyInjection;

use FOS\JsRoutingBundle\DependencyInjection\FOSJsRoutingExtension;
use FOS\JsRoutingBundle\DependencyInjection\SerializerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class SerializerCompilerPassTest
 *
 * @author Miguel Angel Garzón <magarzon@gmail.com>
 */
class SerializerCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Symfony\Component\DependencyInjection\ContainerBuilder')) {
            $this->markTestSkipped('The DependencyInjection component is not available.');
        }
    }

    public function testSerializerInConfig()
    {
        $container = $this->load([['serializer' => 'test.serializer.service']]);

        $compilerPass = new SerializerCompilerPass();
        $compilerPass->process($container);

        $this->assertTrue($container->hasAlias('fos_js_routing.serializer'));
    }

    public function testSerializerDefined()
    {
        $container = $this->load([]);

        $container->register('serializer', Serializer::class);

        $compilerPass = new SerializerCompilerPass();
        $compilerPass->process($container);

        $this->assertTrue($container->hasAlias('fos_js_routing.serializer'));
    }

    public function testSerializerNotDefined()
    {
        $container = $this->load([]);

        $compilerPass = new SerializerCompilerPass();
        $compilerPass->process($container);

        $this->assertTrue($container->hasDefinition('fos_js_routing.serializer'));
    }

    private function load(array $configs)
    {
        $container = new ContainerBuilder();

        $extension = new FOSJsRoutingExtension();
        $extension->load($configs, $container);

        return $container;
    }
}
