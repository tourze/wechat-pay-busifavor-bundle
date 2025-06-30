<?php

namespace WechatPayBusifavorBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Request\GetCouponRequest;

class GetCouponRequestTest extends TestCase
{
    private string $couponCode = 'COUPON123';
    private string $openid = 'openid123';
    private string $appid = 'appid123';
    private GetCouponRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetCouponRequest($this->couponCode, $this->openid, $this->appid);
    }

    public function testGetRequestPath(): void
    {
        $expectedPath = 'v3/marketing/busifavor/users/' . $this->openid . '/coupons/' . $this->couponCode;
        $this->assertEquals($expectedPath, $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('GET', $this->request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('appid', $options['query']);
        $this->assertEquals($this->appid, $options['query']['appid']);
    }

    public function testImplementsRequestInterface(): void
    {
        $this->assertInstanceOf('HttpClientBundle\Request\RequestInterface', $this->request);
    }

    public function testConstructorWithDifferentValues(): void
    {
        $couponCode = 'TESTCOUPON456';
        $openid = 'testopenid456';
        $appid = 'testappid456';
        
        $request = new GetCouponRequest($couponCode, $openid, $appid);
        
        $expectedPath = 'v3/marketing/busifavor/users/' . $openid . '/coupons/' . $couponCode;
        $this->assertEquals($expectedPath, $request->getRequestPath());
        
        $options = $request->getRequestOptions();
        $this->assertEquals($appid, $options['query']['appid']);
    }
}