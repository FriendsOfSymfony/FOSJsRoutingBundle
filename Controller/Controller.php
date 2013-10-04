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
use FOS\JsRoutingBundle\Util\CacheControlConfig;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag;

/**
 * Controller class.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class Controller
{
    /**
     * @var mixed
     */
    protected $serializer;

    /**
     * @var ExposedRoutesExtractorInterface
     */
    protected $exposedRoutesExtractor;

    /**
     * @var CacheControlConfig
     */
    protected $cacheControlConfig;

    /**
     * @var boolean
     */
    protected $debug;

    /**
     * Default constructor.
     *
     * @param mixed                           $serializer             Any object with a serialize($data, $format) method
     * @param ExposedRoutesExtractorInterface $exposedRoutesExtractor The extractor service.
     * @param array                           $cacheControl
     * @param boolean                         $debug
     */
    public function __construct($serializer, ExposedRoutesExtractorInterface $exposedRoutesExtractor, array $cacheControl = array(), $debug = false)
    {
        $this->serializer             = $serializer;
        $this->exposedRoutesExtractor = $exposedRoutesExtractor;
        $this->cacheControlConfig     = new CacheControlConfig($cacheControl);
        $this->debug                  = $debug;
    }

    /**
     * indexAction action.
     */
    public function indexAction(Request $request, $_format)
    {
        if (version_compare(strtolower(Kernel::VERSION), '2.1.0-dev', '<')) {
            if (null !== $session = $request->getSession()) {
                // keep current flashes for one more request
                $session->setFlashes($session->getFlashes());
            }
        } else {
            $session = $request->getSession();

            if ($request->hasPreviousSession() && $session->getFlashBag() instanceof AutoExpireFlashBag) {
                // keep current flashes for one more request if using AutoExpireFlashBag
                $session->getFlashBag()->setAll($session->getFlashBag()->peekAll());
            }
        }

        $cache = new ConfigCache($this->exposedRoutesExtractor->getCachePath($request->getLocale()), $this->debug);

        if (!$cache->isFresh()) {
            $content = $this->serializer->serialize(
                new RoutesResponse(
                    $this->exposedRoutesExtractor->getBaseUrl(),
                    $this->exposedRoutesExtractor->getRoutes(),
                    $this->exposedRoutesExtractor->getPrefix($request->getLocale()),
                    $this->exposedRoutesExtractor->getHost(),
                    $this->exposedRoutesExtractor->getScheme()
                ),
                'json'
            );
            $cache->write($content, $this->exposedRoutesExtractor->getResources());
        }

        $content = file_get_contents((string) $cache);

        if ($callback = $request->query->get('callback')) {
            $content = $callback.'('.$content.');';
        }

        $response = new Response($content, 200, array('Content-Type' => $request->getMimeType($_format)));
        $this->cacheControlConfig->apply($response);

        return $response;
    }
}
