<?php

namespace WechatPayBusifavorBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Request\GetUserCouponsRequest;

class GetUserCouponsRequestTest extends TestCase
{
    private string $openid = 'openid123';
    private string $appid = 'appid123';

    public function testGetRequestPath(): void
    {
        $request = new GetUserCouponsRequest($this->openid, $this->appid);
        $expectedPath = 'v3/marketing/busifavor/users/' . $this->openid . '/coupons';
        $this->assertEquals($expectedPath, $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetUserCouponsRequest($this->openid, $this->appid);
        $this->assertEquals('GET', $request->getRequestMethod());
    }

    public function testGetRequestOptionsWithRequiredParametersOnly(): void
    {
        $request = new GetUserCouponsRequest($this->openid, $this->appid);
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('appid', $options['query']);
        $this->assertEquals($this->appid, $options['query']['appid']);
        $this->assertCount(1, $options['query']);
    }

    public function testGetRequestOptionsWithAllParameters(): void
    {
        $stockId = 'STOCK123';
        $status = 'SENDED';
        $offset = 10;
        $limit = 20;
        
        $request = new GetUserCouponsRequest(
            $this->openid,
            $this->appid,
            $stockId,
            $status,
            $offset,
            $limit
        );
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        
        $query = $options['query'];
        $this->assertEquals($this->appid, $query['appid']);
        $this->assertEquals($stockId, $query['stock_id']);
        $this->assertEquals($status, $query['status']);
        $this->assertEquals($offset, $query['offset']);
        $this->assertEquals($limit, $query['limit']);
        $this->assertCount(5, $query);
    }

    public function testGetRequestOptionsWithPartialParameters(): void
    {
        $stockId = 'STOCK456';
        $status = 'USED';
        
        $request = new GetUserCouponsRequest(
            $this->openid,
            $this->appid,
            $stockId,
            $status
        );
        
        $options = $request->getRequestOptions();
        $query = $options['query'];
        
        $this->assertEquals($this->appid, $query['appid']);
        $this->assertEquals($stockId, $query['stock_id']);
        $this->assertEquals($status, $query['status']);
        $this->assertArrayNotHasKey('offset', $query);
        $this->assertArrayNotHasKey('limit', $query);
        $this->assertCount(3, $query);
    }

    public function testImplementsRequestInterface(): void
    {
        $request = new GetUserCouponsRequest($this->openid, $this->appid);
        $this->assertInstanceOf('HttpClientBundle\Request\RequestInterface', $request);
    }

    public function testNullOptionalParameters(): void
    {
        $request = new GetUserCouponsRequest(
            $this->openid,
            $this->appid,
            null,
            null,
            null,
            null
        );
        
        $options = $request->getRequestOptions();
        $query = $options['query'];
        
        $this->assertArrayHasKey('appid', $query);
        $this->assertArrayNotHasKey('stock_id', $query);
        $this->assertArrayNotHasKey('status', $query);
        $this->assertArrayNotHasKey('offset', $query);
        $this->assertArrayNotHasKey('limit', $query);
        $this->assertCount(1, $query);
    }
}