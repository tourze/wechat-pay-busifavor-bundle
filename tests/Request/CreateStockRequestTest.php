<?php

namespace WechatPayBusifavorBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Request\CreateStockRequest;

class CreateStockRequestTest extends TestCase
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