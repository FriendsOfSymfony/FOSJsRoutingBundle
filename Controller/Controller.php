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
use Symfony\Component\HttpKernel\Exception\HttpException;

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
    public function __construct(private mixed $serializer, private ExposedRoutesExtractorInterface $exposedRoutesExtractor, array $cacheControl = [], private bool $debug = false)
    {
        $this->cacheControlConfig = new CacheControlConfig($cacheControl);
    }

    public function indexAction(Request $request, $_format): Response
    {
        $session = $request->hasSession() ? $request->getSession() : null;

        if ($request->hasPreviousSession() && $session->getFlashBag() instanceof AutoExpireFlashBag) {
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

        $routesResponse = new RoutesResponse(
            $this->exposedRoutesExtractor->getBaseUrl(),
            $exposedRoutes,
            $this->exposedRoutesExtractor->getPrefix($request->getLocale()),
            $this->exposedRoutesExtractor->getHost(),
            $this->exposedRoutesExtractor->getPort(),
            $this->exposedRoutesExtractor->getScheme(),
            $request->getLocale(),
            $request->query->has('domain') ? explode(',', $request->query->get('domain')) : []
        );

        $content = $this->serializer->serialize($routesResponse, 'json');

        if (null !== $callback = $request->query->get('callback')) {
            $validator = new \JsonpCallbackValidator();
            if (!$validator->validate($callback)) {
                throw new HttpException(400, 'Invalid JSONP callback value');
            }

            $content = '/**/'.$callback.'('.$content.');';
        }

        $response = new Response($content, 200, ['Content-Type' => $request->getMimeType($_format)]);
        $this->cacheControlConfig->apply($response);

        return $response;
    }
}
