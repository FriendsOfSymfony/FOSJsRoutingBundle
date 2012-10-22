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
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag;
use JMS\I18nRoutingBundle\Router\I18nLoader;

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

    protected $bundles;

    protected $debug;

    /**
     * Default constructor.
     *
     * @param mixed $serializer any object with a serialize($data, $format) method
     * @param \FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface $exposedRoutesExtractor   The extractor service.
     * @param string $cacheDir
     * @param array $bundles
     * @param boolean $debug
     */
    public function __construct($serializer, ExposedRoutesExtractorInterface $exposedRoutesExtractor, $cacheDir, $bundles, $debug = false)
    {
        $this->serializer = $serializer;
        $this->exposedRoutesExtractor = $exposedRoutesExtractor;
        $this->cacheDir = $cacheDir;
        $this->bundles = $bundles;
        $this->debug = $debug;
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

        $cachePath = $this->cacheDir.'/fosJsRouting';
        if (!file_exists($cachePath)) {
            mkdir($cachePath);
        }

        $prefix = '';

        if (isset($this->bundles['JMSI18nRoutingBundle'])) {
            $prefix = $request->getLocale().I18nLoader::ROUTING_PREFIX;
            $cachePath = $cachePath . '/data.' . $request->getLocale() . '.json';
        } else {
            $cachePath = $cachePath . '/data.json';
        }

        $cache = new ConfigCache($cachePath, $this->debug);
        if (!$cache->isFresh()) {
            $content = $this->serializer->serialize(
                new RoutesResponse(
                    $this->exposedRoutesExtractor->getBaseUrl(),
                    $this->exposedRoutesExtractor->getRoutes(),
                    $prefix
                ),
                'json'
            );
            $cache->write($content, $this->exposedRoutesExtractor->getResources());
        }

        $content = file_get_contents((string) $cache);

        if ($callback = $request->query->get('callback')) {
            $content = $callback.'('.$content.');';
        }

        return new Response($content, 200, array('Content-Type' => $request->getMimeType($_format)));
    }
}
