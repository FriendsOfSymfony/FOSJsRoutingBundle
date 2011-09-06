<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Controller;

use FOS\JsRoutingBundle\Response\RoutesResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface;


/**
 * Controller class.
 *
 * @author      William DURAND <william.durand1@gmail.com>
 */
class Controller
{
    protected $serializer;

    /**
     * @var \FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface
     */
    protected $exposedRoutesExtractor;

    /**
     * Default constructor.
     * @param mixed $serializer any object with a serialize($data, $format) method
     * @param \FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface    The extractor service.
     */
    public function __construct($serializer, ExposedRoutesExtractorInterface $exposedRoutesExtractor)
    {
        $this->serializer = $serializer;
        $this->exposedRoutesExtractor = $exposedRoutesExtractor;
    }

    /**
     * indexAction action.
     */
    public function indexAction(Request $request, $_format)
    {
        return new Response(
            $this->serializer->serialize(new RoutesResponse(
                    $this->exposedRoutesExtractor->getBaseUrl(),
                    $this->exposedRoutesExtractor->getRoutes()
                ),
                $_format
            ),
            200,
            array(
                'Content-Type' => $request->getMimeType($_format),
            )
        );
    }
}
