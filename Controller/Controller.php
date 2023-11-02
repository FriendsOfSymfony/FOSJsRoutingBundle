<?php

declare(strict_types=1);

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
use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Controller class.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class Controller
{
    protected CacheControlConfig $cacheControlConfig;

    /**
     * Default constructor.
     *
     * @param object                          $serializer             Any object with a serialize($data, $format) method
     * @param ExposedRoutesExtractorInterface $exposedRoutesExtractor the extractor service
     * @param bool                            $debug
     */
    public function __construct(
        private RoutesResponse $routesResponse,
        private mixed $serializer,
        private ExposedRoutesExtractorInterface $exposedRoutesExtractor,
        array $cacheControl = [],
        private bool $debug = false,
    ) {
        $this->cacheControlConfig = new CacheControlConfig($cacheControl);
    }

    public function indexAction(Request $request, $_format): Response
    {
        if (!$request->attributes->getBoolean('_stateless') && $request->hasSession()
            && ($session = $request->getSession())->isStarted() && $session->getFlashBag() instanceof AutoExpireFlashBag
        ) {
            // keep current flashes for one more request if using AutoExpireFlashBag
            $session->getFlashBag()->setAll($session->getFlashBag()->peekAll());
        }

        $cache = new ConfigCache($this->exposedRoutesExtractor->getCachePath($request->getLocale()), $this->debug);

        if (!$cache->isFresh() || $this->debug) {
            $exposedRoutes = $this->exposedRoutesExtractor->getRoutes();
            $serializedRoutes = $this->serializer->serialize($exposedRoutes, 'json');
            $cache->write($serializedRoutes, $this->exposedRoutesExtractor->getResources());
        } else {
            $path = method_exists($cache, 'getPath') ? $cache->getPath() : (string) $cache;
            $serializedRoutes = file_get_contents($path);
            $exposedRoutes = $this->serializer->deserialize(
                $serializedRoutes,
                'Symfony\Component\Routing\RouteCollection',
                'json'
            );
        }

        $this->routesResponse->setBaseUrl($this->exposedRoutesExtractor->getBaseUrl());
        $this->routesResponse->setRoutes($exposedRoutes);
        $this->routesResponse->setPrefix($this->exposedRoutesExtractor->getPrefix($request->getLocale()));
        $this->routesResponse->setHost($this->exposedRoutesExtractor->getHost());
        $this->routesResponse->setPort($this->exposedRoutesExtractor->getPort());
        $this->routesResponse->setScheme($this->exposedRoutesExtractor->getScheme());
        $this->routesResponse->setLocale($request->getLocale());
        $this->routesResponse->setDomains($request->query->has('domain') ? explode(',', $request->query->get('domain')) : []);

        $content = $this->serializer->serialize($this->routesResponse, 'json');

        if (null !== $callback = $request->query->get('callback')) {
            if (!\JsonpCallbackValidator::validate($callback)) {
                throw new BadRequestHttpException('Invalid JSONP callback value');
            }

            $content = '/**/'.$callback.'('.$content.');';
        }

        $response = new Response($content, 200, ['Content-Type' => $request->getMimeType($_format)]);
        $this->cacheControlConfig->apply($response);

        return $response;
    }
}
