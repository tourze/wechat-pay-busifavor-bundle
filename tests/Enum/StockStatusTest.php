<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatPayBusifavorBundle\Enum\StockStatus;

/**
 * @internal
 */
#[CoversClass(StockStatus::class)]
final class StockStatusTest extends AbstractEnumTestCase
{
    #[TestWith([StockStatus::UNAUDIT, 'UNAUDIT', '未激活'])]
    #[TestWith([StockStatus::CHECKING, 'CHECKING', '审核中'])]
    #[TestWith([StockStatus::AUDIT_REJECT, 'AUDIT_REJECT', '审核失败'])]
    #[TestWith([StockStatus::AUDIT_SUCCESS, 'AUDIT_SUCCESS', '通过审核'])]
    #[TestWith([StockStatus::ONGOING, 'ONGOING', '进行中'])]
    #[TestWith([StockStatus::PAUSED, 'PAUSED', '已暂停'])]
    #[TestWith([StockStatus::STOPPED, 'STOPPED', '已停止'])]
    #[TestWith([StockStatus::EXPIRED, 'EXPIRED', '已作废'])]
    public function testValueAndLabel(StockStatus $case, string $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $case->value);
        $this->assertSame($expectedLabel, $case->getLabel());
    }

    public function testCases(): void
    {
        $cases = StockStatus::cases();

        $this->assertCount(8, $cases);
        $this->assertContainsOnlyInstancesOf(StockStatus::class, $cases);
    }

    #[TestWith(['UNAUDIT', StockStatus::UNAUDIT])]
    #[TestWith(['CHECKING', StockStatus::CHECKING])]
    #[TestWith(['AUDIT_REJECT', StockStatus::AUDIT_REJECT])]
    #[TestWith(['AUDIT_SUCCESS', StockStatus::AUDIT_SUCCESS])]
    #[TestWith(['ONGOING', StockStatus::ONGOING])]
    #[TestWith(['PAUSED', StockStatus::PAUSED])]
    #[TestWith(['STOPPED', StockStatus::STOPPED])]
    #[TestWith(['EXPIRED', StockStatus::EXPIRED])]
    public function testFromValue(string $value, StockStatus $expected): void
    {
        $this->assertSame($expected, StockStatus::from($value));
    }

    public function testFromValueThrowsExceptionForInvalidValue(): void
    {
        $this->expectException(\ValueError::class);
        StockStatus::from('INVALID');
    }

    #[TestWith(['UNAUDIT', StockStatus::UNAUDIT])]
    #[TestWith(['CHECKING', StockStatus::CHECKING])]
    #[TestWith(['AUDIT_REJECT', StockStatus::AUDIT_REJECT])]
    #[TestWith(['AUDIT_SUCCESS', StockStatus::AUDIT_SUCCESS])]
    #[TestWith(['ONGOING', StockStatus::ONGOING])]
    #[TestWith(['PAUSED', StockStatus::PAUSED])]
    #[TestWith(['STOPPED', StockStatus::STOPPED])]
    #[TestWith(['EXPIRED', StockStatus::EXPIRED])]
    public function testTryFromValueValid(string $value, StockStatus $expected): void
    {
        $this->assertSame($expected, StockStatus::tryFrom($value));
    }

    #[TestWith(['INVALID'])]
    #[TestWith(['invalid'])]
    #[TestWith([''])]
    public function testTryFromValueInvalid(string $value): void
    {
        $this->assertNull(StockStatus::tryFrom($value));
    }

    public function testTryFromValueNull(): void
    {
        // PHP 8.1+ 枚举的 tryFrom() 方法不接受 null 值，这是预期行为
        // 测试传入 null 时的行为应该是抛出 TypeError
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('WechatPayBusifavorBundle\Enum\StockStatus::tryFrom(): Argument #1 ($value) must be of type string, null given');

        /** @phpstan-ignore argument.type */
        $result = StockStatus::tryFrom(null);
        // 这行永远不会执行，但保持变量使用以消除PHPStan警告
        unset($result);
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (StockStatus $case) => $case->value, StockStatus::cases());
        $this->assertSame($values, array_unique($values));
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (StockStatus $case) => $case->getLabel(), StockStatus::cases());
        $this->assertSame($labels, array_unique($labels));
    }

    #[TestWith([StockStatus::UNAUDIT, 'UNAUDIT', '未激活'])]
    #[TestWith([StockStatus::CHECKING, 'CHECKING', '审核中'])]
    #[TestWith([StockStatus::AUDIT_REJECT, 'AUDIT_REJECT', '审核失败'])]
    #[TestWith([StockStatus::AUDIT_SUCCESS, 'AUDIT_SUCCESS', '通过审核'])]
    #[TestWith([StockStatus::ONGOING, 'ONGOING', '进行中'])]
    #[TestWith([StockStatus::PAUSED, 'PAUSED', '已暂停'])]
    #[TestWith([StockStatus::STOPPED, 'STOPPED', '已停止'])]
    #[TestWith([StockStatus::EXPIRED, 'EXPIRED', '已作废'])]
    public function testToArray(StockStatus $case, string $expectedValue, string $expectedLabel): void
    {
        $array = $case->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame($expectedValue, $array['value']);
        $this->assertSame($expectedLabel, $array['label']);
    }
}
