<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Serializer\Normalizer;

use FOS\JsRoutingBundle\Response\RoutesResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class RoutesResponseNormalizer
 */
class RoutesResponseNormalizer implements NormalizerInterface
{
    /**
     * @var RouteCollectionNormalizer
     */
    protected $routeCollectionNormalizer;

    /**
     * @param RouteCollectionNormalizer $routeCollectionNormalizer
     */
    public function __construct(RouteCollectionNormalizer $routeCollectionNormalizer)
    {
        $this->routeCollectionNormalizer = $routeCollectionNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return array(
            'base_url' => $object->getBaseUrl(),
            'routes' => $this->routeCollectionNormalizer->normalize($object->getRoutes()),
            'prefix' => $object->getPrefix(),
            'host' => $object->getHost(),
            'scheme' => $object->getScheme(),
        );
    }

    /**
     * Checks if the given class implements the NormalizableInterface.
     *
     * @param mixed  $data   Data to normalize.
     * @param string $format The format being (de-)serialized from or into.
     *
     * @return Boolean
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RoutesResponse
            && $this->routeCollectionNormalizer->supportsNormalization($data->getRoutes());
    }
}
