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
use FOS\JsRoutingBundle\Response\RoutesResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Dumps routes to the filesystem.
 *
 * @author Benjamin Dulau <benjamin.dulau@anonymation.com>
 */
class DumpCommand extends Command
{
    protected static $defaultName = 'fos:js-routing:dump';

    /**
     * @var ExposedRoutesExtractorInterface
     */
    private $extractor;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $requestContextBaseUrl;

    public function __construct(ExposedRoutesExtractorInterface $extractor, SerializerInterface $serializer, $projectDir, $requestContextBaseUrl = null)
    {
        $this->extractor = $extractor;
        $this->serializer = $serializer;
        $this->projectDir = $projectDir;
        $this->requestContextBaseUrl = $requestContextBaseUrl;

        parent::__construct();
    }

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
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'Format to output routes in. js to wrap the response in a callback, json for raw json output. Callback is ignored when format is json',
                'js'
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
                'pretty-print',
                'p',
                InputOption::VALUE_NONE,
                'Pretty print the JSON.'
            )
            ->addOption(
                'domain',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Specify expose domain',
                array()
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if(!in_array($input->getOption('format'), array('js', 'json'))) {
            $output->writeln('<error>Invalid format specified. Use js or json.</error>');
            return 1;
        }

        $callback = $input->getOption('callback');
        if(empty($callback)) {
            $output->writeln('<error>If you include --callback it must not be empty. Do you perhaps want --format=json</error>');
            return 1;
        }

        $output->writeln('Dumping exposed routes.');
        $output->writeln('');

        $this->doDump($input, $output);
        return 0;
    }

    /**
     * Performs the routes dump.
     *
     * @param InputInterface  $input  The command input
     * @param OutputInterface $output The command output
     */
    private function doDump(InputInterface $input, OutputInterface $output)
    {
        $domain = $input->getOption('domain');

        $extractor = $this->extractor;
        $serializer = $this->serializer;
        $targetPath = $input->getOption('target') ?:
            sprintf(
                '%s/web/js/fos_js_routes%s.%s',
                $this->projectDir,
                empty($domain) ? '' : ('_' . implode('_', $domain)),
                $input->getOption('format')
            );
        
        if (!is_dir($dir = dirname($targetPath))) {
            $output->writeln('<info>[dir+]</info>  ' . $dir);
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException('Unable to create directory ' . $dir);
            }
        }

        $output->writeln('<info>[file+]</info> ' . $targetPath);

        $baseUrl = null !== $this->requestContextBaseUrl ?
            $this->requestContextBaseUrl :
            $this->extractor->getBaseUrl()
        ;

        if ($input->getOption('pretty-print')) {
            $params = array('json_encode_options' => JSON_PRETTY_PRINT);
        } else {
            $params = array();
        }

        $content = $serializer->serialize(
            new RoutesResponse(
                $baseUrl,
                $extractor->getRoutes(),
                $extractor->getPrefix($input->getOption('locale')),
                $extractor->getHost(),
                $extractor->getPort(),
                $extractor->getScheme(),
                $input->getOption('locale'),
                $domain
            ),
            'json',
            $params
        );

        if('js' == $input->getOption('format')) {
            $content = sprintf("%s(%s);", $input->getOption('callback'), $content);
        }

        if (false === @file_put_contents($targetPath, $content)) {
            throw new \RuntimeException('Unable to write file ' . $targetPath);
        }
    }
}
