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

namespace FOS\JsRoutingBundle\Tests\Serializer\Normalizer;

use FOS\JsRoutingBundle\Serializer\Normalizer\RouteCollectionNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteCollectionNormalizerTest.
 */
class RouteCollectionNormalizerTest extends TestCase
{
    public function testSupportsNormalization(): void
    {
        $normalizer = new RouteCollectionNormalizer();

        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
        $this->assertTrue($normalizer->supportsNormalization(new RouteCollection()));
    }

    public function testNormalize(): void
    {
        $normalizer = new RouteCollectionNormalizer();

        $routes = new RouteCollection();
        $routes->add('literal', new Route('/literal'));
        $routes->add('blog_post', new Route('/blog-post/{slug}'));
        $routes->add('list', new Route('/literal'));

        $expected = [
            'literal' => [
                'path' => '/literal',
                'host' => '',
                'defaults' => [],
                'requirements' => [],
                'options' => [
                    'compiler_class' => 'Symfony\Component\Routing\RouteCompiler',
                ],
                'schemes' => [],
                'methods' => [],
                'condition' => '',
            ],
            'blog_post' => [
                'path' => '/blog-post/{slug}',
                'host' => '',
                'defaults' => [],
                'requirements' => [],
                'options' => [
                    'compiler_class' => 'Symfony\Component\Routing\RouteCompiler',
                ],
                'schemes' => [],
                'methods' => [],
                'condition' => '',
            ],
            'list' => [
                'path' => '/literal',
                'host' => '',
                'defaults' => [],
                'requirements' => [],
                'options' => [
                    'compiler_class' => 'Symfony\Component\Routing\RouteCompiler',
                ],
                'schemes' => [],
                'methods' => [],
                'condition' => '',
            ],
        ];

        $this->assertSame($expected, $normalizer->normalize($routes));
    }
}
