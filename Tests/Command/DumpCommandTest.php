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
use FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractor;
use JMS\I18nRoutingBundle\Router\I18nLoader;
use JMS\I18nRoutingBundle\Router\I18nRouter;
use JMS\I18nRoutingBundle\Router\DefaultRouteExclusionStrategy;
use JMS\I18nRoutingBundle\Router\DefaultPatternGenerationStrategy;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader as TranslationLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

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

    /**
     * @dataProvider getLocaleOnlyData
     */
    public function testExecuteWithLocaleOnly($locale, $expected)
    {
        $container = new Container();
        $router = $this->getRouter($container);

        $tmpDir = sys_get_temp_dir();
        $container->setParameter('kernel.root_dir', $tmpDir);

        $container->set('fos_js_routing.extractor', new ExposedRoutesExtractor($router, array(), null));
        $container->set('fos_js_routing.serializer', new Serializer(array(new GetSetMethodNormalizer()), array(new JsonEncoder())));

        $command = new DumpCommand();
        $command->setContainer($container);

        $tester = new CommandTester($command);
        $tester->execute(array('--locale' => $locale, '--locale-only' => true));

        $filename = sprintf('%s/../web/js/fos_js_routes%s.js', $tmpDir, sprintf('.%s', $locale));
        $this->assertEquals($expected, @file_get_contents($filename));
    }

    public function getLocaleOnlyData()
    {
        return array(
            array(
                'en',
                'fos.Router.setData({"baseUrl":"","routes":{"homepage":{"tokens":[["text","\/homepage-english"]],"defaults":[],"requirements":[],"hosttokens":[]},"foo":{"tokens":[["text","\/foo-en"]],"defaults":[],"requirements":[],"hosttokens":[]},"bar":{"tokens":[["text","\/bar-en"]],"defaults":[],"requirements":[],"hosttokens":[]},"untranslated_route":{"tokens":[["text","\/not-translated"]],"defaults":[],"requirements":[],"hosttokens":[]},"english_only":{"tokens":[["text","\/english-only"]],"defaults":[],"requirements":[],"hosttokens":[]},"non_i18n_route":{"tokens":[["text","\/non-i18n-route"]],"defaults":[],"requirements":[],"hosttokens":[]}},"prefix":"","host":"localhost","scheme":"http"});'
            ),
            array(
                'es',
                'fos.Router.setData({"baseUrl":"","routes":{"homepage":{"tokens":[["text","\/homepage-spanish"]],"defaults":[],"requirements":[],"hosttokens":[]},"foo":{"tokens":[["text","\/foo-es"]],"defaults":[],"requirements":[],"hosttokens":[]},"bar":{"tokens":[["text","\/bar-es"]],"defaults":[],"requirements":[],"hosttokens":[]},"untranslated_route":{"tokens":[["text","\/not-translated"]],"defaults":[],"requirements":[],"hosttokens":[]},"spanish_only":{"tokens":[["text","\/spanish-only"]],"defaults":[],"requirements":[],"hosttokens":[]},"non_i18n_route":{"tokens":[["text","\/non-i18n-route"]],"defaults":[],"requirements":[],"hosttokens":[]}},"prefix":"","host":"localhost","scheme":"http"});'
            ),
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage You must provide a locale to use with --locale-only.
     */
    public function testExecuteWithLocaleOnlyWithoutLocale()
    {
        $command = new DumpCommand();
        $command->setContainer($this->container);

        $tester = new CommandTester($command);
        $tester->execute(array('--locale-only' => true));
    }

    private function getRouter($container, $config = 'routing.yml', $translator = null, $localeResolver = null)
    {
        $container->set('routing.loader', new YamlFileLoader(new FileLocator(__DIR__.'/Fixture')));

        if (null === $translator) {
            $translator = new Translator('en', new MessageSelector());
            $translator->setFallbackLocale('en');
            $translator->addLoader('yml', new TranslationLoader());
            $translator->addResource('yml', __DIR__.'/Fixture/routes.en.yml', 'en', 'routes');
            $translator->addResource('yml', __DIR__.'/Fixture/routes.es.yml', 'es', 'routes');
        }

        $container->set('i18n_loader', new I18nLoader(new DefaultRouteExclusionStrategy(), new DefaultPatternGenerationStrategy('custom', $translator, array('en', 'es'), sys_get_temp_dir())));

        $router = new I18nRouter($container, $config);
        $router->setI18nLoaderId('i18n_loader');
        $router->setDefaultLocale('en');

        if (null !== $localeResolver) {
            $router->setLocaleResolver($localeResolver);
        }

        return $router;
    }
}
