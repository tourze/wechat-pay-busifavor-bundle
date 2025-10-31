<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\DTO\CouponData;
use WechatPayBusifavorBundle\Enum\CouponStatus;

/**
 * @internal
 */
#[CoversClass(CouponData::class)]
final class CouponDataTest extends TestCase
{
    public function testConstruct(): void
    {
        $expireTime = new \DateTimeImmutable('2024-12-31 23:59:59');
        $useTime = new \DateTimeImmutable('2024-12-01 12:00:00');

        $couponData = new CouponData(
            couponCode: 'COUP123456',
            stockId: 'STOCK001',
            openid: 'user_openid_123',
            status: CouponStatus::SENDED,
            expireTime: $expireTime,
            useTime: $useTime
        );

        $this->assertSame('COUP123456', $couponData->couponCode);
        $this->assertSame('STOCK001', $couponData->stockId);
        $this->assertSame('user_openid_123', $couponData->openid);
        $this->assertSame(CouponStatus::SENDED, $couponData->status);
        $this->assertSame($expireTime, $couponData->expireTime);
        $this->assertSame($useTime, $couponData->useTime);
    }

    public function testConstructWithNullTimes(): void
    {
        $couponData = new CouponData(
            couponCode: 'COUP123456',
            stockId: 'STOCK001',
            openid: 'user_openid_123',
            status: CouponStatus::SENDED
        );

        $this->assertSame('COUP123456', $couponData->couponCode);
        $this->assertSame('STOCK001', $couponData->stockId);
        $this->assertSame('user_openid_123', $couponData->openid);
        $this->assertSame(CouponStatus::SENDED, $couponData->status);
        $this->assertNull($couponData->expireTime);
        $this->assertNull($couponData->useTime);
    }

    /**
     * @param array<string, mixed> $inputData
     */
    #[DataProvider('provideFromArrayData')]
    public function testFromArray(array $inputData, string $openid, CouponData $expected): void
    {
        $result = CouponData::fromArray($inputData, $openid);

        $this->assertSame($expected->couponCode, $result->couponCode);
        $this->assertSame($expected->stockId, $result->stockId);
        $this->assertSame($expected->openid, $result->openid);
        $this->assertSame($expected->status, $result->status);

        // Compare time values
        if (null === $expected->expireTime) {
            $this->assertNull($result->expireTime);
        } else {
            $this->assertInstanceOf(\DateTimeImmutable::class, $result->expireTime);
            $this->assertSame($expected->expireTime->getTimestamp(), $result->expireTime->getTimestamp());
        }

        if (null === $expected->useTime) {
            $this->assertNull($result->useTime);
        } else {
            $this->assertInstanceOf(\DateTimeImmutable::class, $result->useTime);
            $this->assertSame($expected->useTime->getTimestamp(), $result->useTime->getTimestamp());
        }
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, CouponData}>
     */
    public static function provideFromArrayData(): iterable
    {
        yield 'complete_data' => [
            [
                'coupon_code' => 'COUP123456',
                'stock_id' => 'STOCK001',
                'status' => 'SENDED',
                'expire_time' => '2024-12-31 23:59:59',
                'use_time' => '2024-12-01 12:00:00',
            ],
            'user_openid_123',
            new CouponData(
                couponCode: 'COUP123456',
                stockId: 'STOCK001',
                openid: 'user_openid_123',
                status: CouponStatus::SENDED,
                expireTime: new \DateTimeImmutable('@1735689599'), // 2024-12-31 23:59:59 UTC
                useTime: new \DateTimeImmutable('@1733054400') // 2024-12-01 12:00:00 UTC
            ),
        ];

        yield 'minimal_data' => [
            [],
            'user_openid_456',
            new CouponData(
                couponCode: '',
                stockId: '',
                openid: 'user_openid_456',
                status: CouponStatus::SENDED,
                expireTime: null,
                useTime: null
            ),
        ];

        yield 'partial_data' => [
            [
                'coupon_code' => 'COUP789012',
                'status' => 'USED',
            ],
            'user_openid_789',
            new CouponData(
                couponCode: 'COUP789012',
                stockId: '',
                openid: 'user_openid_789',
                status: CouponStatus::USED,
                expireTime: null,
                useTime: null
            ),
        ];

        yield 'invalid_time_format' => [
            [
                'coupon_code' => 'COUP789012',
                'stock_id' => 'STOCK002',
                'expire_time' => 'invalid-date',
                'use_time' => 'also-invalid',
            ],
            'user_openid_789',
            new CouponData(
                couponCode: 'COUP789012',
                stockId: 'STOCK002',
                openid: 'user_openid_789',
                status: CouponStatus::SENDED,
                expireTime: null,
                useTime: null
            ),
        ];
    }

    public function testFromArrayWithDifferentStatusValues(): void
    {
        $data = ['status' => 'USED'];
        $result = CouponData::fromArray($data, 'test_openid');

        $this->assertSame(CouponStatus::USED, $result->status);
    }

    public function testFromArrayWithInvalidStatus(): void
    {
        $this->expectException(\ValueError::class);
        CouponData::fromArray(['status' => 'INVALID_STATUS'], 'test_openid');
    }

    public function testFromArrayWithValidTimestamps(): void
    {
        $data = [
            'expire_time' => '@1735689599', // Unix timestamp with @ prefix
            'use_time' => '@1733054400',
        ];

        $result = CouponData::fromArray($data, 'test_openid');

        $this->assertInstanceOf(\DateTimeImmutable::class, $result->expireTime);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result->useTime);
        $this->assertSame(1735689599, $result->expireTime->getTimestamp());
        $this->assertSame(1733054400, $result->useTime->getTimestamp());
    }
}
