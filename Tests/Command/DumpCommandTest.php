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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DumpCommandTest extends TestCase
{
    protected $extractor;
    protected $router;
    private $serializer;

    public function setUp()
    {
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
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}'));

        $command = new DumpCommand($this->extractor, $this->serializer, '/root/dir');

        $tester = new CommandTester($command);
        $tester->execute(array('--target' => '/tmp/dump-command-test'));

        $this->assertContains('Dumping exposed routes.', $tester->getDisplay());
        $this->assertContains('[file+] /tmp/dump-command-test', $tester->getDisplay());

        $this->assertEquals('fos.Router.setData({"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""});', file_get_contents('/tmp/dump-command-test'));
    }

    public function testExecuteCallbackOption()
    {
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}'));

        $command = new DumpCommand($this->extractor, $this->serializer, '/root/dir');

        $tester = new CommandTester($command);
        $tester->execute(array(
            '--target' => '/tmp/dump-command-test',
            '--callback' => 'test',
        ));

        $this->assertContains('Dumping exposed routes.', $tester->getDisplay());
        $this->assertContains('[file+] /tmp/dump-command-test', $tester->getDisplay());

        $this->assertEquals('test({"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""});', file_get_contents('/tmp/dump-command-test'));
    }

    public function testExecuteFormatOption()
    {
        $json = '{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}';

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue($json));

        $command = new DumpCommand($this->extractor, $this->serializer, '/root/dir');

        $tester = new CommandTester($command);
        $tester->execute(array(
            '--target' => '/tmp/dump-command-test',
            '--format' => 'json',
        ));

        $this->assertContains('Dumping exposed routes.', $tester->getDisplay());
        $this->assertContains('[file+] /tmp/dump-command-test', $tester->getDisplay());

        $this->assertEquals($json, file_get_contents('/tmp/dump-command-test'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to create directory /root/dir/../web/js
     */
    public function testExecuteUnableToCreateDirectory()
    {
        $command = new DumpCommand($this->extractor, $this->serializer, '/root/dir');

        $tester = new CommandTester($command);
        $tester->execute(array());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to write file /tmp
     */
    public function testExecuteUnableToWriteFile()
    {
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}'));

        $command = new DumpCommand($this->extractor, $this->serializer, '/root/dir');

        $tester = new CommandTester($command);
        $tester->execute(array('--target' => '/tmp'));
    }
}
