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
use FOS\JsRoutingBundle\Serializer\Normalizer\RoutesResponseNormalizer;
use PHPUnit\Framework\TestCase;

class RoutesResponseNormalizerTest extends TestCase
{
    public function testSupportsNormalization(): void
    {
        $normalizer = new RoutesResponseNormalizer(new RouteCollectionNormalizer());
        $response = $this->getMockBuilder('FOS\JsRoutingBundle\Response\RoutesResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
        $this->assertTrue($normalizer->supportsNormalization($response));
    }

    public function testNormalize(): void
    {
        $normalizer = new RoutesResponseNormalizer(new RouteCollectionNormalizer());
        $response = $this->getMockBuilder('FOS\JsRoutingBundle\Response\RoutesResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $response->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue([]));

        $response->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('baseUrl'));

        $response->expects($this->once())
            ->method('getPrefix')
            ->will($this->returnValue('prefix'));

        $response->expects($this->once())
            ->method('getHost')
            ->will($this->returnValue('host'));

        $response->expects($this->once())
            ->method('getScheme')
            ->will($this->returnValue('scheme'));

        $response->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('locale'));

        $expected = [
            'base_url' => 'baseUrl',
            'routes' => [],
            'prefix' => 'prefix',
            'host' => 'host',
            'port' => null,
            'scheme' => 'scheme',
            'locale' => 'locale',
        ];

        $this->assertSame($expected, $normalizer->normalize($response));
    }
}
