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
     */
    public function normalize($data, $format = null, array $context = array())
    {
        $collection = array();
        foreach ($data->all() as $name => $route) {
            $compiledRoute = $route->compile();
            $defaults      = array_intersect_key(
                $route->getDefaults(),
                array_fill_keys($compiledRoute->getVariables(), null)
            );

            $collection[$name] = array(
                'tokens'       => $compiledRoute->getTokens(),
                'defaults'     => $defaults,
                'requirements' => $route->getRequirements(),
                'hosttokens'   => method_exists($compiledRoute, 'getHostTokens') ? $compiledRoute->getHostTokens() : array(),
            );
        }

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RouteCollection;
    }
}
