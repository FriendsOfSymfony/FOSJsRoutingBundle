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

namespace FOS\JsRoutingBundle\Util;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

class CacheControlConfig
{
    public function __construct(private array $parameters = [])
    {
    }

    public function apply(Response $response): void
    {
        if (empty($this->parameters['enabled'])) {
            return;
        }

        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        $this->parameters['public'] ? $response->setPublic() : $response->setPrivate();

        if (is_int($this->parameters['maxage'])) {
            $response->setMaxAge($this->parameters['maxage']);
        }

        if (is_int($this->parameters['smaxage'])) {
            $response->setSharedMaxAge($this->parameters['smaxage']);
        }

        if (null !== $this->parameters['expires']) {
            $response->setExpires(new \DateTime($this->parameters['expires']));
        }

        if (!empty($this->parameters['vary'])) {
            $response->setVary($this->parameters['vary']);
        }
    }
}
