<?php

namespace WechatPayBusifavorBundle\Tests;

use HttpClientBundle\HttpClientBundle;
use PHPUnit\Framework\TestCase;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use WechatPayBundle\WechatPayBundle;
use WechatPayBusifavorBundle\WechatPayBusifavorBundle;

class WechatPayBusifavorBundleTest extends TestCase
{
    public function testGetBundleDependencies(): void
    {
        $dependencies = WechatPayBusifavorBundle::getBundleDependencies();
        
        $this->assertArrayHasKey(DoctrineIndexedBundle::class, $dependencies);
        $this->assertArrayHasKey(HttpClientBundle::class, $dependencies);
        $this->assertArrayHasKey(WechatPayBundle::class, $dependencies);
        
        $this->assertEquals(['all' => true], $dependencies[DoctrineIndexedBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[HttpClientBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[WechatPayBundle::class]);
    }
} 