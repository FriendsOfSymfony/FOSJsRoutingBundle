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

use FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand;
use Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Route;

/**
 * A console command for retrieving information about exposed routes.
 *
 * @author      William DURAND <william.durand1@gmail.com>
 */
class RouterDebugExposedCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'fos:js-routing:debug';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('name', InputArgument::OPTIONAL, 'A route name'),
                new InputOption('show-controllers', null, InputOption::VALUE_NONE, 'Show assigned controllers in overview'),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt'),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw route(s)'),
            ))
            ->setName('fos:js-routing:debug')
            ->setDescription('Displays currently exposed routes for an application')
            ->setHelp(<<<EOF
The <info>fos:js-routing:debug</info> command displays an application's routes which will be available via JavaScript.

  <info>php app/console fos:js-routing:debug</info>

You can alternatively specify a route name as an argument to get more info about that specific route:

  <info>php app/console fos:js-routing:debug my_route</info>

EOF
            )
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ExposedRoutesExtractorInterface $extractor */
        $extractor = $this->getContainer()->get('fos_js_routing.extractor');

        if ($name = $input->getArgument('name')) {
            /** @var Route $route */
            $route = $this->getContainer()->get('router')->getRouteCollection()->get($name);

            if (!$route) {
                throw new \InvalidArgumentException(sprintf('The route "%s" does not exist.', $name));
            }

            if (!$extractor->isRouteExposed($route, $name)) {
                throw new \InvalidArgumentException(sprintf('The route "%s" was found, but it is not an exposed route.', $name));
            }

            $helper = new DescriptorHelper();
            $helper->describe($output, $route, array(
                'format'           => $input->getOption('format'),
                'raw_text'         => $input->getOption('raw'),
                'show_controllers' => $input->getOption('show-controllers'),
            ));
        } else {
            $helper = new DescriptorHelper();
            $helper->describe($output, $extractor->getRoutes(), array(
                'format'           => $input->getOption('format'),
                'raw_text'         => $input->getOption('raw'),
                'show_controllers' => $input->getOption('show-controllers'),
            ));
        }
    }
}
