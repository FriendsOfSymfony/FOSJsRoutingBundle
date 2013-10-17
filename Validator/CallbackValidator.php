<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Validator;

/**
 * Most of the code above took inspiration from:
 * https://gist.github.com/ptz0n/1217080.
 */
class CallbackValidator
{
    private $reservedKeywords = array(
        'break',
        'do',
        'instanceof',
        'typeof',
        'case',
        'else',
        'new',
        'var',
        'catch',
        'finally',
        'return',
        'void',
        'continue',
        'for',
        'switch',
        'while',
        'debugger',
        'function',
        'this',
        'with',
        'default',
        'if',
        'throw',
        'delete',
        'in',
        'try',
        'class',
        'enum',
        'extends',
        'super',
        'const',
        'export',
        'import',
        'implements',
        'let',
        'private',
        'public',
        'yield',
        'interface',
        'package',
        'protected',
        'static',
        'null',
        'true',
        'false',
    );

    public function isValid($callback)
    {
        foreach (explode('.', $callback) as $identifier) {
            if (!preg_match('/^[a-zA-Z_$][0-9a-zA-Z_$]*(?:\[(?:".+"|\'.+\'|\d+)\])*?$/', $identifier)) {
                return false;
            }

            if (in_array($identifier, $this->reservedKeywords)) {
                return false;
            }
        }

        return true;
    }
}
