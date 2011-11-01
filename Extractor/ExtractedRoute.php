<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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