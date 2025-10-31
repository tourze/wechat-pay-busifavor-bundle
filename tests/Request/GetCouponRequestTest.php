<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatPayBusifavorBundle\Request\GetCouponRequest;

/**
 * @internal
 */
#[CoversClass(GetCouponRequest::class)]
final class GetCouponRequestTest extends RequestTestCase
{
    private string $couponCode = 'COUPON123';

    private string $openid = 'openid123';

    private string $appid = 'appid123';

    private GetCouponRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

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
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('appid', $query);
        $this->assertEquals($this->appid, $query['appid']);
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
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertEquals($appid, $query['appid']);
    }
}
