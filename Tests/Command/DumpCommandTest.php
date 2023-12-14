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

use FOS\JsRoutingBundle\Command\DumpCommand;
use FOS\JsRoutingBundle\Response\RoutesResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Router;
use FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractor;

class DumpCommandTest extends TestCase
{
    protected RoutesResponse $routesResponse;
    protected $extractor;
    protected $router;
    private $serializer;

    public function setUp(): void
    {
        $this->routesResponse = $this->getMockBuilder(RoutesResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->extractor = $this->getMockBuilder(ExposedRoutesExtractor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializer = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute(): void
    {
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}'));

        $command = new DumpCommand(
            $this->routesResponse,
            $this->extractor,
            $this->serializer,
            '/root/dir',
        );

        $tester = new CommandTester($command);
        $tester->execute(['--target' => '/tmp/dump-command-test']);

        $this->assertStringContainsString('Dumping exposed routes.', $tester->getDisplay());
        $this->assertStringContainsString('[file+] /tmp/dump-command-test', $tester->getDisplay());

        $this->assertEquals('fos.Router.setData({"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""});', file_get_contents('/tmp/dump-command-test'));
    }

    public function testExecuteCallbackOption(): void
    {
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}'));

        $command = new DumpCommand(
            $this->routesResponse,
            $this->extractor,
            $this->serializer,
            '/root/dir',
        );

        $tester = new CommandTester($command);
        $tester->execute([
            '--target' => '/tmp/dump-command-test',
            '--callback' => 'test',
        ]);

        $this->assertStringContainsString('Dumping exposed routes.', $tester->getDisplay());
        $this->assertStringContainsString('[file+] /tmp/dump-command-test', $tester->getDisplay());

        $this->assertEquals('test({"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""});', file_get_contents('/tmp/dump-command-test'));
    }

    public function testExecuteFormatOption(): void
    {
        $json = '{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}';

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue($json));

        $command = new DumpCommand(
            $this->routesResponse,
            $this->extractor,
            $this->serializer,
            '/root/dir',
        );

        $tester = new CommandTester($command);
        $tester->execute([
            '--target' => '/tmp/dump-command-test',
            '--format' => 'json',
        ]);

        $this->assertStringContainsString('Dumping exposed routes.', $tester->getDisplay());
        $this->assertStringContainsString('[file+] /tmp/dump-command-test', $tester->getDisplay());

        $this->assertEquals($json, file_get_contents('/tmp/dump-command-test'));
    }

    public function testExecuteUnableToCreateDirectory(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to create directory /root/dir/public/js');

        $command = new DumpCommand(
            $this->routesResponse,
            $this->extractor,
            $this->serializer,
            '/root/dir',
        );

        $tester = new CommandTester($command);
        $tester->execute([]);
    }

    public function testExecuteUnableToWriteFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to write file /tmp');
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{"base_url":"","routes":{"literal":{"tokens":[["text","\/homepage"]],"defaults":[],"requirements":[],"hosttokens":[]},"blog":{"tokens":[["variable","\/","[^\/]++","slug"],["text","\/blog-post"]],"defaults":[],"requirements":[],"hosttokens":[["text","localhost"]]}},"prefix":"","host":"","scheme":""}'));

        $command = new DumpCommand(
            $this->routesResponse,
            $this->extractor,
            $this->serializer,
            '/root/dir',
        );

        $tester = new CommandTester($command);
        $tester->execute(['--target' => '/tmp']);
    }
}
