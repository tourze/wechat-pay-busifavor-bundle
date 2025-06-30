<?php

namespace WechatPayBusifavorBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Request\UseCouponRequest;

class UseCouponRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new UseCouponRequest([]);
        $this->assertEquals('v3/marketing/busifavor/coupons/use', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new UseCouponRequest([]);
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $requestData = [
            'coupon_code' => 'COUPON123',
            'use_time' => '2023-01-01T10:00:00+08:00',
            'use_request_no' => 'USE123456',
            'appid' => 'appid123',
            'openid' => 'openid123',
        ];

        $request = new UseCouponRequest($requestData);
        $options = $request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertEquals($requestData, $options['json']);
    }

    public function testImplementsRequestInterface(): void
    {
        $request = new UseCouponRequest([]);
        $this->assertInstanceOf('HttpClientBundle\Request\RequestInterface', $request);
    }

    public function testEmptyRequestData(): void
    {
        $request = new UseCouponRequest([]);
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertEmpty($options['json']);
    }

    public function testComplexRequestData(): void
    {
        $requestData = [
            'coupon_code' => 'COUPON789',
            'use_time' => '2023-12-31T23:59:59+08:00',
            'use_request_no' => 'USE789012',
            'appid' => 'appid456',
            'openid' => 'openid456',
            'stock_id' => 'STOCK456',
            'merchant_id' => '1234567890',
            'additional_data' => [
                'order_id' => 'ORDER123',
                'amount' => 100,
            ],
        ];

        $request = new UseCouponRequest($requestData);
        $options = $request->getRequestOptions();

        $this->assertEquals($requestData, $options['json']);
    }
}