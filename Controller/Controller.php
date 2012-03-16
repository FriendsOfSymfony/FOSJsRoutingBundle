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

use FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface;
use FOS\JsRoutingBundle\Response\RoutesResponse;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    protected $cacheDir;

    protected $debug;

    /**
     * Default constructor.
     *
     * @param mixed $serializer any object with a serialize($data, $format) method
     * @param \FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface $exposedRoutesExtractor   The extractor service.
     * @param string $cacheDir
     * @param boolean $debug
     */
    public function __construct($serializer, ExposedRoutesExtractorInterface $exposedRoutesExtractor, $cacheDir, $debug = false)
    {
        $this->serializer = $serializer;
        $this->exposedRoutesExtractor = $exposedRoutesExtractor;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * indexAction action.
     */
    public function indexAction(Request $request, $_format)
    {
        $cache = new ConfigCache($this->cacheDir.'/fosJsRouting.json', $this->debug);
        if (!$cache->isFresh()) {
            $content = $this->serializer->serialize(
                new RoutesResponse(
                    $this->exposedRoutesExtractor->getBaseUrl(),
                    $request->getLocale(),
                    $this->exposedRoutesExtractor->getRoutes()
                ),
                'json'
            );
            $cache->write($content, $this->exposedRoutesExtractor->getResources());
        }

        $content = file_get_contents((string) $cache);

        if ($callback = $request->query->get('callback')) {
            $content = $callback.'('.$content.')';
        }

        return new Response($content, 200, array('Content-Type' => $request->getMimeType($_format)));
    }
}
