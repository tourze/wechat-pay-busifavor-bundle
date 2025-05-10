<?php

namespace WechatPayBusifavorBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Enum\CouponStatus;

class CouponTest extends TestCase
{
    private Coupon $coupon;

    protected function setUp(): void
    {
        $this->coupon = new Coupon();
    }

    public function testIdGetterSetter(): void
    {
        $reflection = new \ReflectionProperty(Coupon::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($this->coupon, 123);

        $this->assertEquals(123, $this->coupon->getId());
    }

    public function testCouponCodeGetterSetter(): void
    {
        $couponCode = 'test_coupon_code_123456';
        $result = $this->coupon->setCouponCode($couponCode);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($couponCode, $this->coupon->getCouponCode());
    }

    public function testStockIdGetterSetter(): void
    {
        $stockId = 'test_stock_id_123456';
        $result = $this->coupon->setStockId($stockId);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($stockId, $this->coupon->getStockId());
    }

    public function testOpenidGetterSetter(): void
    {
        $openid = 'test_openid_123456';
        $result = $this->coupon->setOpenid($openid);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($openid, $this->coupon->getOpenid());
    }

    public function testStatusGetterSetter(): void
    {
        $status = CouponStatus::SENDED;
        $result = $this->coupon->setStatus($status);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($status, $this->coupon->getStatus());
    }

    public function testExpiryTimeGetterSetter(): void
    {
        $time = new \DateTimeImmutable('2023-12-31 23:59:59');
        $result = $this->coupon->setExpiryTime($time);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($time, $this->coupon->getExpiryTime());

        // 测试 null 值
        $result = $this->coupon->setExpiryTime(null);
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertNull($this->coupon->getExpiryTime());
    }

    public function testUsedTimeGetterSetter(): void
    {
        $time = new \DateTimeImmutable('2023-01-15 12:00:00');
        $result = $this->coupon->setUsedTime($time);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($time, $this->coupon->getUsedTime());

        // 测试 null 值
        $result = $this->coupon->setUsedTime(null);
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertNull($this->coupon->getUsedTime());
    }

    public function testUseRequestNoGetterSetter(): void
    {
        $requestNo = 'test_request_no_123456';
        $result = $this->coupon->setUseRequestNo($requestNo);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($requestNo, $this->coupon->getUseRequestNo());

        // 测试 null 值
        $result = $this->coupon->setUseRequestNo(null);
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertNull($this->coupon->getUseRequestNo());
    }

    public function testUseInfoGetterSetter(): void
    {
        $useInfo = [
            'wechatpay_use_time' => '2023-01-15T12:00:00+08:00',
            'use_time' => '2023-01-15T12:00:00+08:00',
        ];
        $result = $this->coupon->setUseInfo($useInfo);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($useInfo, $this->coupon->getUseInfo());

        // 测试 null 值
        $result = $this->coupon->setUseInfo(null);
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertNull($this->coupon->getUseInfo());
    }

    public function testTransactionIdGetterSetter(): void
    {
        $transactionId = 'test_transaction_id_123456';
        $result = $this->coupon->setTransactionId($transactionId);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($transactionId, $this->coupon->getTransactionId());

        // 测试 null 值
        $result = $this->coupon->setTransactionId(null);
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertNull($this->coupon->getTransactionId());
    }
} 