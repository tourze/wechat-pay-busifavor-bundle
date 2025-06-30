<?php

namespace WechatPayBusifavorBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Request\GetStockRequest;

class GetStockRequestTest extends TestCase
{
    private string $stockId = 'STOCK123';
    private GetStockRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetStockRequest($this->stockId);
    }

    public function testGetRequestPath(): void
    {
        $expectedPath = 'v3/marketing/busifavor/stocks/' . $this->stockId;
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
        $this->assertEmpty($options);
    }

    public function testImplementsRequestInterface(): void
    {
        $this->assertInstanceOf('HttpClientBundle\Request\RequestInterface', $this->request);
    }

    public function testConstructorWithDifferentStockId(): void
    {
        $stockId = 'TESTSTOCK456';
        $request = new GetStockRequest($stockId);
        
        $expectedPath = 'v3/marketing/busifavor/stocks/' . $stockId;
        $this->assertEquals($expectedPath, $request->getRequestPath());
    }
}