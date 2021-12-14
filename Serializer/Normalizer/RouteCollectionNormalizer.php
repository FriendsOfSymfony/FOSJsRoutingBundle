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

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class RouteCollectionNormalizer
 */
class RouteCollectionNormalizer implements NormalizerInterface
{
    /**
     * {@inheritDoc}
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($data, $format = null, array $context = array())
    {
        $collection = array();

        foreach ($data->all() as $name => $route) {
            $collection[$name] = array(
                'path'         => $route->getPath(),
                'host'         => $route->getHost(),
                'defaults'     => $route->getDefaults(),
                'requirements' => $route->getRequirements(),
                'options'      => $route->getOptions(),
                'schemes'      => $route->getSchemes(),
                'methods'      => $route->getMethods(),
                'condition'    => method_exists($route, 'getCondition') ? $route->getCondition() : '',
            );
        }

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof RouteCollection;
    }
}
