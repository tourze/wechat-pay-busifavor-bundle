<?php

namespace WechatPayBusifavorBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Enum\StockStatus;

class StockStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('UNAUDIT', StockStatus::UNAUDIT->value);
        $this->assertSame('CHECKING', StockStatus::CHECKING->value);
        $this->assertSame('AUDIT_REJECT', StockStatus::AUDIT_REJECT->value);
        $this->assertSame('AUDIT_SUCCESS', StockStatus::AUDIT_SUCCESS->value);
        $this->assertSame('ONGOING', StockStatus::ONGOING->value);
        $this->assertSame('PAUSED', StockStatus::PAUSED->value);
        $this->assertSame('STOPPED', StockStatus::STOPPED->value);
        $this->assertSame('EXPIRED', StockStatus::EXPIRED->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('未激活', StockStatus::UNAUDIT->getLabel());
        $this->assertSame('审核中', StockStatus::CHECKING->getLabel());
        $this->assertSame('审核失败', StockStatus::AUDIT_REJECT->getLabel());
        $this->assertSame('通过审核', StockStatus::AUDIT_SUCCESS->getLabel());
        $this->assertSame('进行中', StockStatus::ONGOING->getLabel());
        $this->assertSame('已暂停', StockStatus::PAUSED->getLabel());
        $this->assertSame('已停止', StockStatus::STOPPED->getLabel());
        $this->assertSame('已作废', StockStatus::EXPIRED->getLabel());
    }

    public function testGetOptions(): void
    {
        $options = StockStatus::getOptions();

        $this->assertCount(8, $options);
        $this->assertSame('未激活', $options['UNAUDIT']);
        $this->assertSame('审核中', $options['CHECKING']);
        $this->assertSame('审核失败', $options['AUDIT_REJECT']);
        $this->assertSame('通过审核', $options['AUDIT_SUCCESS']);
        $this->assertSame('进行中', $options['ONGOING']);
        $this->assertSame('已暂停', $options['PAUSED']);
        $this->assertSame('已停止', $options['STOPPED']);
        $this->assertSame('已作废', $options['EXPIRED']);
    }

    public function testCases(): void
    {
        $cases = StockStatus::cases();

        $this->assertCount(8, $cases);
        $this->assertContainsOnlyInstancesOf(StockStatus::class, $cases);
    }

    public function testFromValue(): void
    {
        $status = StockStatus::from('ONGOING');
        $this->assertSame(StockStatus::ONGOING, $status);

        $status = StockStatus::from('PAUSED');
        $this->assertSame(StockStatus::PAUSED, $status);
    }

    public function testTryFromValue(): void
    {
        $status = StockStatus::tryFrom('ONGOING');
        $this->assertSame(StockStatus::ONGOING, $status);

        $status = StockStatus::tryFrom('INVALID');
        $this->assertNull($status);
    }
}