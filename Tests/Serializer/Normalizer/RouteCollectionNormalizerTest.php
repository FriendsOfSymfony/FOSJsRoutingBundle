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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * Class RouteCollectionNormalizerTest
 */
class RouteCollectionNormalizerTest extends TestCase
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
                'path'         => '/literal',
                'host'         => '',
                'defaults'     => array(),
                'requirements' => array(),
                'options'      => array(
                    'compiler_class' => 'Symfony\Component\Routing\RouteCompiler'
                ),
                'schemes'   => array(),
                'methods'   => array(),
                'condition' => '',
            ),
            'blog_post' => array(
                'path'         => '/blog-post/{slug}',
                'host'         => '',
                'defaults'     => array(),
                'requirements' => array(),
                'options'      => array(
                    'compiler_class' => 'Symfony\Component\Routing\RouteCompiler'
                ),
                'schemes'   => array(),
                'methods'   => array(),
                'condition' => '',
            ),
            'list' => array(
                'path'         => '/literal',
                'host'         => '',
                'defaults'     => array(),
                'requirements' => array(),
                'options'      => array(
                    'compiler_class' => 'Symfony\Component\Routing\RouteCompiler',
                ),
                'schemes'   => array(),
                'methods'   => array(),
                'condition' => '',
            ),
        );

        $this->assertSame($expected, $normalizer->normalize($routes));
    }
}
