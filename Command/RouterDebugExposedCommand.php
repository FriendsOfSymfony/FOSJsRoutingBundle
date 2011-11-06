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

use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $extractor = $this->getContainer()->get('fos_js_routing.extractor');

        $routes = array();
        foreach ($extractor->getExposedRoutes() as $name => $route) {
            $routes[$name] = $route->compile();
        }

        if ($input->getArgument('name')) {
            $this->outputRoute($output, $routes, $input->getArgument('name'));
        } else {
            $this->outputRoutes($output, $routes);
        }
    }
}
