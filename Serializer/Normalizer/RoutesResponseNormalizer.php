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

namespace FOS\JsRoutingBundle\Serializer\Normalizer;

use FOS\JsRoutingBundle\Response\RoutesResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class RoutesResponseNormalizer.
 */
class RoutesResponseNormalizer implements NormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'base_url' => $object->getBaseUrl(),
            'routes' => $object->getRoutes(),
            'prefix' => $object->getPrefix(),
            'host' => $object->getHost(),
            'port' => $object->getPort(),
            'scheme' => $object->getScheme(),
            'locale' => $object->getLocale(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof RoutesResponse;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [RoutesResponse::class => true];
    }
}
