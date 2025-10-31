<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatPayBusifavorBundle\Enum\CouponStatus;

/**
 * @internal
 */
#[CoversClass(CouponStatus::class)]
final class CouponStatusTest extends AbstractEnumTestCase
{
    #[TestWith([CouponStatus::SENDED, 'SENDED', '可用'])]
    #[TestWith([CouponStatus::USED, 'USED', '已核销'])]
    #[TestWith([CouponStatus::EXPIRED, 'EXPIRED', '已过期'])]
    #[TestWith([CouponStatus::DEACTIVATED, 'DEACTIVATED', '已失效'])]
    public function testValueAndLabel(CouponStatus $case, string $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $case->value);
        $this->assertSame($expectedLabel, $case->getLabel());
    }

    public function testCases(): void
    {
        $cases = CouponStatus::cases();

        $this->assertCount(4, $cases);
        $this->assertContainsOnlyInstancesOf(CouponStatus::class, $cases);
    }

    #[TestWith(['SENDED', CouponStatus::SENDED])]
    #[TestWith(['USED', CouponStatus::USED])]
    #[TestWith(['EXPIRED', CouponStatus::EXPIRED])]
    #[TestWith(['DEACTIVATED', CouponStatus::DEACTIVATED])]
    public function testFromValue(string $value, CouponStatus $expected): void
    {
        $this->assertSame($expected, CouponStatus::from($value));
    }

    public function testFromValueThrowsExceptionForInvalidValue(): void
    {
        $this->expectException(\ValueError::class);
        CouponStatus::from('INVALID');
    }

    #[TestWith(['SENDED', CouponStatus::SENDED])]
    #[TestWith(['USED', CouponStatus::USED])]
    #[TestWith(['EXPIRED', CouponStatus::EXPIRED])]
    #[TestWith(['DEACTIVATED', CouponStatus::DEACTIVATED])]
    public function testTryFromValueValid(string $value, CouponStatus $expected): void
    {
        $this->assertSame($expected, CouponStatus::tryFrom($value));
    }

    #[TestWith(['INVALID'])]
    #[TestWith(['invalid'])]
    #[TestWith([''])]
    public function testTryFromValueInvalid(string $value): void
    {
        $this->assertNull(CouponStatus::tryFrom($value));
    }

    public function testTryFromValueNull(): void
    {
        // PHP 8.1+ 枚举的 tryFrom() 方法不接受 null 值，这是预期行为
        // 测试传入 null 时的行为应该是抛出 TypeError
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('WechatPayBusifavorBundle\Enum\CouponStatus::tryFrom(): Argument #1 ($value) must be of type string, null given');

        /** @phpstan-ignore argument.type */
        $result = CouponStatus::tryFrom(null);
        // 这行永远不会执行，但保持变量使用以消除PHPStan警告
        unset($result);
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (CouponStatus $case) => $case->value, CouponStatus::cases());
        $this->assertSame($values, array_unique($values));
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (CouponStatus $case) => $case->getLabel(), CouponStatus::cases());
        $this->assertSame($labels, array_unique($labels));
    }

    #[TestWith([CouponStatus::SENDED, 'SENDED', '可用'])]
    #[TestWith([CouponStatus::USED, 'USED', '已核销'])]
    #[TestWith([CouponStatus::EXPIRED, 'EXPIRED', '已过期'])]
    #[TestWith([CouponStatus::DEACTIVATED, 'DEACTIVATED', '已失效'])]
    public function testToArray(CouponStatus $case, string $expectedValue, string $expectedLabel): void
    {
        $array = $case->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame($expectedValue, $array['value']);
        $this->assertSame($expectedLabel, $array['label']);
    }
}
