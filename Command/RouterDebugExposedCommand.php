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
use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand;
use Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * A console command for retrieving information about exposed routes.
 *
 * @author      William DURAND <william.durand1@gmail.com>
 */
class RouterDebugExposedCommand extends RouterDebugCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('fos:js-routing:debug')
            ->setAliases(array()) // reset the aliases used by the parent command in Symfony 2.6+
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
        if ($input->getArgument('name')) {
            $route = $this->getContainer()->get('router')->getRouteCollection()->get($input->getArgument('name'));

            if (!$route) {
                throw new \InvalidArgumentException(sprintf('The route "%s" does not exist.', $input->getArgument('name')));
            }

            $exposedRoutes = $extractor->getExposedRoutes();
            if (!isset($exposedRoutes[$input->getArgument('name')])) {
                throw new \InvalidArgumentException(sprintf('The route "%s" was found, but it is not an exposed route.', $input->getArgument('name')));
            }

            if (!class_exists('Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper')) {
                // BC layer for Symfony 2.3 and lower
                $this->outputRoute($output, $input->getArgument('name'));
            } else {
                $helper = new DescriptorHelper();
                $helper->describe($output, $route, array(
                    'format' => $input->getOption('format'),
                    'raw_text' => $input->getOption('raw'),
                    'show_controllers' => $input->getOption('show-controllers'),
                ));
            }
        } else {
            if (!class_exists('Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper')) {
                // BC layer for Symfony 2.3 and lower
                $this->outputRoutes($output, $extractor->getExposedRoutes());
            } else {
                $routeCollection = new RouteCollection();
                foreach ($extractor->getExposedRoutes() as $name => $route) {
                    $routeCollection->add($name, $route);
                }

                $helper = new DescriptorHelper();
                $helper->describe($output, $routeCollection, array(
                    'format' => $input->getOption('format'),
                    'raw_text' => $input->getOption('raw'),
                    'show_controllers' => $input->getOption('show-controllers'),
                ));
            }
        }
    }
}
