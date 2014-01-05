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

use FOS\JsRoutingBundle\Command\DumpCommand;
use Symfony\Component\Console\Tester\CommandTester;

class DumpCommandTest extends \PHPUnit_Framework_TestCase
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

        $this->serializer = $this->getMockBuilder('Symfony\Component\Serializer\SerializerInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with('fos_js_routing.extractor')
            ->will($this->returnValue($this->extractor));

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}'));

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('fos_js_routing.serializer')
            ->will($this->returnValue($this->serializer));

        $command = new DumpCommand();
        $command->setContainer($this->container);

        $tester = new CommandTester($command);
        $tester->execute(array('--target' => '/tmp/dump-command-test'));

        $this->assertContains('Dumping exposed routes.', $tester->getDisplay());
        $this->assertContains('[file+] /tmp/dump-command-test', $tester->getDisplay());

        $this->assertEquals('fos.Router.setData({"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""});', file_get_contents('/tmp/dump-command-test'));
    }

    public function testExecuteCallbackOption()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with('fos_js_routing.extractor')
            ->will($this->returnValue($this->extractor));

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}'));

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('fos_js_routing.serializer')
            ->will($this->returnValue($this->serializer));

        $command = new DumpCommand();
        $command->setContainer($this->container);

        $tester = new CommandTester($command);
        $tester->execute(array(
            '--target' => '/tmp/dump-command-test',
            '--callback' => 'test',
        ));

        $this->assertContains('Dumping exposed routes.', $tester->getDisplay());
        $this->assertContains('[file+] /tmp/dump-command-test', $tester->getDisplay());

        $this->assertEquals('test({"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""});', file_get_contents('/tmp/dump-command-test'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to create directory /../web/js
     */
    public function testExecuteUnableToCreateDirectory()
    {
        $command = new DumpCommand();
        $command->setContainer($this->container);

        $tester = new CommandTester($command);
        $tester->execute(array());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to write file /tmp
     */
    public function testExecuteUnableToWriteFile()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with('fos_js_routing.extractor')
            ->will($this->returnValue($this->extractor));

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}'));

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('fos_js_routing.serializer')
            ->will($this->returnValue($this->serializer));

        $command = new DumpCommand();
        $command->setContainer($this->container);

        $tester = new CommandTester($command);
        $tester->execute(array('--target' => '/tmp'));
    }
}
