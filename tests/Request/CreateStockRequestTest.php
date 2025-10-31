<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatPayBusifavorBundle\Request\CreateStockRequest;

/**
 * @internal
 */
#[CoversClass(CreateStockRequest::class)]
final class CreateStockRequestTest extends RequestTestCase
{
    public function testGetRequestPath(): void
    {
        $request = new CreateStockRequest([]);
        $this->assertEquals('v3/marketing/busifavor/stocks', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new CreateStockRequest([]);
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $requestData = [
            'stock_name' => 'Test Stock',
            'belong_merchant' => '1234567890',
            'available_begin_time' => '2023-01-01T00:00:00+08:00',
            'available_end_time' => '2023-12-31T23:59:59+08:00',
            'stock_use_rule' => [
                'max_amount' => 100,
                'min_amount' => 10,
            ],
            'coupon_use_rule' => [
                'use_method' => 'OFF',
            ],
        ];

        $request = new CreateStockRequest($requestData);
        $options = $request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertEquals($requestData, $options['json']);
    }

    public function testImplementsRequestInterface(): void
    {
        $request = new CreateStockRequest([]);
        $this->assertInstanceOf('HttpClientBundle\Request\RequestInterface', $request);
    }
}
