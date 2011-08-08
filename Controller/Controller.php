<?php

namespace FOS\JsRoutingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

use FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface;


/**
 * Controller class.
 *
 * @package     FOSJsRoutingBundle
 * @subpackage  Controller
 * @author      William DURAND <william.durand1@gmail.com>
 */
class Controller
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    protected $engine;
    /**
     * @var \FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface
     */
    protected $exposedRoutesExtractor;

    /**
     * Default constructor.
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface        The template engine.
     * @param \FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface    The extractor service.
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
        return $this->engine->renderResponse('FOSJsRoutingBundle::index.' . $_format . '.twig', array(
            'var_prefix'        => '{',
            'var_suffix'        => '}',
            'prefix'            => $this->exposedRoutesExtractor->getBaseUrl(),
            'exposed_routes'    => $this->exposedRoutesExtractor->getRoutes(),
        ));
    }
}
