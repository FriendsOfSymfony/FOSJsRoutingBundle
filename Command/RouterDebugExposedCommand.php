<?php

namespace Bazinga\ExposeRoutingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command for retrieving information about exposed routes.
 *
 * @package     ExposeRoutingBundle
 * @subpackage  Command
 * @author William DURAND <william.durand1@gmail.com>
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
            ->setName('router:debug-exposed')
            ->setDescription('Displays current exposed routes for an application')
            ->setHelp(<<<EOF
The <info>router:debug</info> displays the exposed routes:

<info>router:debug</info>
EOF
            )
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extractor = $this->getContainer()->get('bazinga.exposerouting.extractor');

        $routes = array();
        foreach ($extractor->getRoutes() as $name => $route) {
            $routes[$name] = $route->compile();
        }

        if ($input->getArgument('name')) {
            $this->outputRoute($output, $routes, $input->getArgument('name'));
        } else {
            $this->outputRoutes($output, $routes);
        }
    }
}
