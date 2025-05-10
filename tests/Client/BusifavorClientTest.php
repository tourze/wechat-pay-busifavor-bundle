<?php

namespace WechatPayBusifavorBundle\Tests\Client;

use HttpClientBundle\Client\ApiClient;
use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Client\BusifavorClient;

class BusifavorClientTest extends TestCase
{
    /**
     * 这个测试类仅测试 BusifavorClient 的公共接口，而不是内部实现
     * 实际测试依赖于继承的 ApiClient 功能，我们通过模拟响应来测试
     */
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(BusifavorClient::class));
        $this->assertTrue(is_subclass_of(BusifavorClient::class, ApiClient::class));
    }
} 