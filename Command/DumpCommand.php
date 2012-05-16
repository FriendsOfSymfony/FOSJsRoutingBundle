<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Command;

use FOS\JsRoutingBundle\Response\RoutesResponse;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dumps routes to the filesystem.
 *
 * @author Benjamin Dulau <benjamin.dulau@anonymation.com>
 */
class DumpCommand extends ContainerAwareCommand
{
    private $targetPath;
    private $callback;

    protected function configure()
    {
        $this
            ->setName('fos:js-routing:dump')
            ->setDescription('Dumps exposed routes to the filesystem')
            ->addOption('callback', null, InputOption::VALUE_OPTIONAL, 'Callback function to pass the routes as an argument.')
            ->addOption('target', null, InputOption::VALUE_OPTIONAL, 'Override the target directory to dump routes in.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->callback = $input->getOption('callback') ?: 'fos.Router.setData';
        $this->targetPath = $input->getOption('target') ?:
                sprintf('%s/../web/js', $this->getContainer()->getParameter('kernel.root_dir'));

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Dumping exposed routes.');
        $output->writeln('');

        $this->doDump($output);
    }

    /**
     * @return \FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface
     */
    public function getExposedRoutesExtractor()
    {
        return $this->getContainer()->get('fos_js_routing.extractor');
    }

    /**
     * @return \Symfony\Component\Serializer\Serializer
     */
    public function getSerializer()
    {
        return $this->getContainer()->get('fos_js_routing.serializer');
    }

    /**
     * Performs the routes dump.
     *
     * @param OutputInterface $output The command output
     */
    private function doDump(OutputInterface $output)
    {
        $target = rtrim($this->targetPath, '/') . '/fos_js_routes.js';
        if (!is_dir($dir = dirname($target))) {
            $output->writeln('<info>[dir+]</info>  '.$dir);
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException('Unable to create directory '.$dir);
            }
        }

        $output->writeln('<info>[file+]</info> '.$target);

        $content = $this->getSerializer()->serialize(
            new RoutesResponse(
                $this->getExposedRoutesExtractor()->getBaseUrl(),
                $this->getExposedRoutesExtractor()->getRoutes()
            ),
            'json'
        );

        $content = sprintf("%s(%s);", $this->callback, $content);

        if (false === @file_put_contents($target, $content)) {
            throw new \RuntimeException('Unable to write file '.$target);
        }
    }
}
