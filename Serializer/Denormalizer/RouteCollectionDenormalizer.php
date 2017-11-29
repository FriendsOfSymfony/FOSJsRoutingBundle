<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Serializer\Denormalizer;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RouteCollectionDenormalizer implements DenormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $collection = new RouteCollection();

        foreach ($data as $name => $values) {
            $collection->add($name, new Route(
                $values['path'],
                $values['defaults'],
                $values['requirements'],
                $values['options'],
                $values['host'],
                $values['schemes'],
                $values['methods'],
                $values['condition']
            ));
        }

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (!is_array($data)) {
            return false;
        }
        
        if (count($data) < 1) {
            return true;
        }

        $values = current($data);

        foreach (array('path', 'defaults', 'requirements', 'options', 'host', 'schemes', 'methods', 'condition') as $key) {
            if (!isset($values[$key])) {
                return false;
            }
        }

        return true;
    }
}
