<?php

namespace FOS\JsRoutingBundle\Extractor;

class ExtractedRoute
{
    private $tokens;
    private $defaults;

    public function __construct(array $tokens, array $defaults)
    {
        $this->tokens = $tokens;
        $this->defaults = $defaults;
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }
}