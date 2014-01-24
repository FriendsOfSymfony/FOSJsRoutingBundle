<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\JsRoutingBundle\Tests\Serializer\Normalizer;

use FOS\JsRoutingBundle\Serializer\Normalizer\RouteCollectionNormalizer;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * Class RouteCollectionNormalizerTest
 */
class RouteCollectionNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportsNormalization()
    {
        $normalizer = new RouteCollectionNormalizer();

        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
        $this->assertTrue($normalizer->supportsNormalization(new RouteCollection()));
    }

    public function testNormalize()
    {
        $normalizer = new RouteCollectionNormalizer();

        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal'));
        $routes->add('blog_post', new Route('/blog-post/{slug}'));
        $routes->add('list', new Route('/literal'));

        $expected = array(
            'literal' => array(
                'tokens' => array(
                    array(
                        'text',
                        '/literal',
                    ),
                ),
                'defaults'     => array(),
                'requirements' => array(),
                'hosttokens'   => array(),
            ),
            'blog_post' => array(
                'tokens' => array(
                    array(
                        'variable',
                        '/',
                        '[^/]++',
                        'slug',
                    ),
                    array(
                        'text',
                        '/blog-post',
                    ),
                ),
                'defaults'     => array(),
                'requirements' => array(),
                'hosttokens'   => array(),
            ),
            'list' => array(
                'tokens' => array(
                    array(
                        'text',
                        '/literal',
                    )
                ),
                'defaults'     => array(),
                'requirements' => array(),
                'hosttokens'   => array(),
            )
        );

        $this->assertSame($expected, $normalizer->normalize($routes));
    }
}
