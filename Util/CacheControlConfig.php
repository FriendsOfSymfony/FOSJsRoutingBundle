<?php

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

class CacheControlConfig
{
    /**
     * @var array
     */
    private $parameters;

    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * @param Response $response
     */
    public function apply(Response $response)
    {
        if (empty($this->parameters['enabled'])) {
            return;
        }

        $this->parameters['public'] ? $response->setPublic() : $response->setPrivate();

        if (is_integer($this->parameters['maxage'])) {
            $response->setMaxAge($this->parameters['maxage']);
        }

        if (is_integer($this->parameters['smaxage'])) {
            $response->setSharedMaxAge($this->parameters['smaxage']);
        }

        if ($this->parameters['expires'] !== null) {
            $response->setExpires(new \DateTime($this->parameters['expires']));
        }

        if (!empty($this->parameters['vary'])) {
            $response->setVary($this->parameters['vary']);
        }
    }
}
