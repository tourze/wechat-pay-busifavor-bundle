<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Enum\CouponStatus;

/**
 * @internal
 */
#[CoversClass(Coupon::class)]
final class CouponTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Coupon();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        return [
            'couponCode' => ['couponCode', 'test_value'],
            'stockId' => ['stockId', 'test_value'],
            'status' => ['status', CouponStatus::SENDED],
        ];
    }

    private Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();

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
        $this->coupon->setCouponCode($couponCode);

        $this->assertEquals($couponCode, $this->coupon->getCouponCode());
    }

    public function testStockIdGetterSetter(): void
    {
        $stockId = 'test_stock_id_123456';
        $this->coupon->setStockId($stockId);

        $this->assertEquals($stockId, $this->coupon->getStockId());
    }

    public function testOpenidGetterSetter(): void
    {
        $openid = 'test_openid_123456';
        $this->coupon->setOpenid($openid);

        $this->assertEquals($openid, $this->coupon->getOpenid());
    }

    public function testStatusGetterSetter(): void
    {
        $status = CouponStatus::SENDED;
        $this->coupon->setStatus($status);

        $this->assertEquals($status, $this->coupon->getStatus());
    }

    public function testExpiryTimeGetterSetter(): void
    {
        $time = new \DateTimeImmutable('2023-12-31 23:59:59');
        $this->coupon->setExpiryTime($time);

        $this->assertEquals($time, $this->coupon->getExpiryTime());

        // 测试 null 值
        $this->coupon->setExpiryTime(null);
        $this->assertNull($this->coupon->getExpiryTime());
    }

    public function testUsedTimeGetterSetter(): void
    {
        $time = new \DateTimeImmutable('2023-01-15 12:00:00');
        $this->coupon->setUsedTime($time);

        $this->assertEquals($time, $this->coupon->getUsedTime());

        // 测试 null 值
        $this->coupon->setUsedTime(null);
        $this->assertNull($this->coupon->getUsedTime());
    }

    public function testUseRequestNoGetterSetter(): void
    {
        $requestNo = 'test_request_no_123456';
        $this->coupon->setUseRequestNo($requestNo);

        $this->assertEquals($requestNo, $this->coupon->getUseRequestNo());

        // 测试 null 值
        $this->coupon->setUseRequestNo(null);
        $this->assertNull($this->coupon->getUseRequestNo());
    }

    public function testUseInfoGetterSetter(): void
    {
        $useInfo = [
            'wechatpay_use_time' => '2023-01-15T12:00:00+08:00',
            'use_time' => '2023-01-15T12:00:00+08:00',
        ];
        $this->coupon->setUseInfo($useInfo);

        $this->assertEquals($useInfo, $this->coupon->getUseInfo());

        // 测试 null 值
        $this->coupon->setUseInfo(null);
        $this->assertNull($this->coupon->getUseInfo());
    }

    public function testTransactionIdGetterSetter(): void
    {
        $transactionId = 'test_transaction_id_123456';
        $this->coupon->setTransactionId($transactionId);

        $this->assertEquals($transactionId, $this->coupon->getTransactionId());

        // 测试 null 值
        $this->coupon->setTransactionId(null);
        $this->assertNull($this->coupon->getTransactionId());
    }
}
