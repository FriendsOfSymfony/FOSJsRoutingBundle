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
    /**
     * @var string
     */
    private $targetPath;

    /**
     * @var \FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface
     */
    private $extractor;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;

    protected function configure()
    {
        $this
            ->setName('fos:js-routing:dump')
            ->setDescription('Dumps exposed routes to the filesystem')
            ->addOption(
                'callback',
                null,
                InputOption::VALUE_REQUIRED,
                'Callback function to pass the routes as an argument.',
                'fos.Router.setData'
            )
            ->addOption(
                'target',
                null,
                InputOption::VALUE_OPTIONAL,
                'Override the target directory to dump routes in.'
            )
            ->addOption(
               'locale',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set locale to be used with JMSI18nRoutingBundle.',
                ''
            )
            ->addOption(
                'locale-only',
                null,
                InputOption::VALUE_NONE,
                'Set locale only to be used with JMSI18nRoutingBundle. The option --locale must be set.'
            )
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $localeOnly = $input->getOption('locale-only');
        if (true === $localeOnly && !$input->getOption('locale')) {
            throw new \RuntimeException('You must provide a locale to use with --locale-only.');
        }

        $targetPathLocale = '';
        if ($localeOnly) {
            $targetPathLocale = sprintf('.%s', $input->getOption('locale'));
        }

        $this->targetPath = $input->getOption('target') ?:
            sprintf('%s/../web/js/fos_js_routes%s.js',
                $this->getContainer()->getParameter('kernel.root_dir'),
                $targetPathLocale
            );

        $this->extractor = $this->getContainer()->get('fos_js_routing.extractor');
        $this->serializer = $this->getContainer()->get('fos_js_routing.serializer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Dumping exposed routes.');
        $output->writeln('');

        $this->doDump($input, $output);
    }

    /**
     * Performs the routes dump.
     *
     * @param InputInterface  $input  The command input
     * @param OutputInterface $output The command output
     */
    private function doDump(InputInterface $input, OutputInterface $output)
    {
        if (!is_dir($dir = dirname($this->targetPath))) {
            $output->writeln('<info>[dir+]</info>  ' . $dir);
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException('Unable to create directory ' . $dir);
            }
        }

        $output->writeln('<info>[file+]</info> ' . $this->targetPath);

        $baseUrl = $this->getContainer()->hasParameter('fos_js_routing.request_context_base_url') ?
            $this->getContainer()->getParameter('fos_js_routing.request_context_base_url') :
            $this->extractor->getBaseUrl()
        ;

        $localeOnly = $input->getOption('locale-only');

        $content = $this->serializer->serialize(
            new RoutesResponse(
                $baseUrl,
                $this->extractor->getRoutes(),
                $localeOnly ? '' : $input->getOption('locale'),
                $this->extractor->getHost(),
                $this->extractor->getScheme(),
                $localeOnly ? $input->getOption('locale') : null,
                $localeOnly
            ),
            'json'
        );

        $content = sprintf("%s(%s);", $input->getOption('callback'), $content);

        if (false === @file_put_contents($this->targetPath, $content)) {
            throw new \RuntimeException('Unable to write file ' . $this->targetPath);
        }
    }
}
