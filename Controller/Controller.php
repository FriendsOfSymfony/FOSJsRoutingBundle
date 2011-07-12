<?php

namespace Bazinga\ExposeRoutingBundle\Controller;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

use Bazinga\ExposeRoutingBundle\Service\ExposedRoutesExtractorInterface;


/**
 * Controller class.
 *
 * @package     ExposeRoutingBundle
 * @subpackage  Controller
 * @author William DURAND <william.durand1@gmail.com>
 */
class Controller
{
    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $engine;
    /**
     * @var \Bazinga\ExposeRoutingBundle\Service\ExposedRoutesExtractorInterface
     */
    protected $exposedRoutesExtractor;

    /**
     * Default constructor.
     * @param \Symfony\Component\Templating\EngineInterface   The template engine.
     * @param array Some route names to expose.
     */
    public function __construct(EngineInterface $engine, ExposedRoutesExtractorInterface $exposedRoutesExtractor)
    {
        $this->engine = $engine;
        $this->exposedRoutesExtractor = $exposedRoutesExtractor;
    }

    /**
     * indexAction action.
     */
    public function indexAction($_format)
    {
        return new Response($this->engine->render('BazingaExposeRoutingBundle::index.' . $_format . '.twig', array(
            'var_prefix'        => '{',
            'var_suffix'        => '}',
            'prefix'            => $this->exposedRoutesExtractor->getBaseUrl(),
            'exposed_routes'    => $this->exposedRoutesExtractor->getRoutes(),
        )));
    }
}
