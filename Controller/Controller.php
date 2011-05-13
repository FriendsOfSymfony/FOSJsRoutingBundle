<?php

namespace Bazinga\ExposeRoutingBundle\Controller;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller class.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class Controller
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;
    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $engine;

    /**
     * Default constructor.
     * @param \Symfony\Component\Routing\RouterInterface  The router.
     * @param \Symfony\Component\Templating\EngineInterface   The template engine.
     */
    public function __construct(RouterInterface $router, EngineInterface $engine)
    {
        $this->router = $router;
        $this->engine = $engine;
    }

    /**
     * indexAction action.
     */
    public function indexAction($_format)
    {
        $exposed_routes = array();
        $collection     = $this->router->getRouteCollection();

        foreach ($collection->all() as $name => $route) {
            if ($route->getOption('expose') && true === $route->getOption('expose')) {
                $exposed_routes[$name] = $route;
            }
        }

        return new Response($this->engine->render('BazingaExposeRoutingBundle::index.' . $_format . '.twig', array(
            'prefix'         => $this->router->getContext()->getBaseUrl(),
            'exposed_routes' => $exposed_routes,
        )));
    }
}
