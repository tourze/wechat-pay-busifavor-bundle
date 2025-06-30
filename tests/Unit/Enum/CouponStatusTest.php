<?php

namespace WechatPayBusifavorBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Enum\CouponStatus;

class CouponStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('SENDED', CouponStatus::SENDED->value);
        $this->assertSame('USED', CouponStatus::USED->value);
        $this->assertSame('EXPIRED', CouponStatus::EXPIRED->value);
        $this->assertSame('DEACTIVATED', CouponStatus::DEACTIVATED->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('可用', CouponStatus::SENDED->getLabel());
        $this->assertSame('已核销', CouponStatus::USED->getLabel());
        $this->assertSame('已过期', CouponStatus::EXPIRED->getLabel());
        $this->assertSame('已失效', CouponStatus::DEACTIVATED->getLabel());
    }

    public function testGetOptions(): void
    {
        $options = CouponStatus::getOptions();

        $this->assertCount(4, $options);
        $this->assertSame('可用', $options['SENDED']);
        $this->assertSame('已核销', $options['USED']);
        $this->assertSame('已过期', $options['EXPIRED']);
        $this->assertSame('已失效', $options['DEACTIVATED']);
    }

    public function testCases(): void
    {
        $cases = CouponStatus::cases();

        $this->assertCount(4, $cases);
        $this->assertContainsOnlyInstancesOf(CouponStatus::class, $cases);
    }

    public function testFromValue(): void
    {
        $status = CouponStatus::from('SENDED');
        $this->assertSame(CouponStatus::SENDED, $status);

        $status = CouponStatus::from('USED');
        $this->assertSame(CouponStatus::USED, $status);
    }

    public function testTryFromValue(): void
    {
        $status = CouponStatus::tryFrom('SENDED');
        $this->assertSame(CouponStatus::SENDED, $status);

        $status = CouponStatus::tryFrom('INVALID');
        $this->assertNull($status);
    }
}