<?php

namespace FOS\JsRoutingBundle\CacheControl;

use Symfony\Component\HttpFoundation\Response;

class CacheControl
{
    /** @var bool */
    protected $enabled = false;

    /** @var bool */
    protected $public;

    /** @var mixed */
    protected $expires;

    /** @var int */
    protected $maxage;

    /** @var int */
    protected $smaxage;

    /** @var array */
    protected $vary;

    /**
     * @param bool|array $cacheConfig
     */
    public function __construct($cacheConfig = false)
    {
        if ($cacheConfig === false) {
            return;
        }

        $this->enabled = true;
        $this->public = $cacheConfig['public'];
        $this->expires = $cacheConfig['expires'];
        $this->maxage = $cacheConfig['maxage'];
        $this->smaxage = $cacheConfig['smaxage'];
        $this->vary = $cacheConfig['vary'];
    }

    /**
     * @param Response $response
     *
     * @return Response
     */
    public function setCacheHeaders(Response $response)
    {
        if ($this->enabled === false) {
            return $response;
        }

        $this->public ? $response->setPublic() : $response->setPrivate();

        if (is_integer($this->maxage)) {
            $response->setMaxAge($this->maxage);
        }

        if (is_integer($this->smaxage)) {
            $response->setSharedMaxAge($this->smaxage);
        }

        if ($this->expires !== null) {
            $response->setExpires(new \DateTime($this->expires));
        }

        if (!empty($this->vary)) {
            $response->setVary($this->vary);
        }

        return $response;
    }
}