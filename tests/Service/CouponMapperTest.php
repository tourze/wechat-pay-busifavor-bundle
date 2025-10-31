<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\DTO\CouponData;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Enum\CouponStatus;
use WechatPayBusifavorBundle\Service\CouponMapper;

/**
 * @internal
 */
#[CoversClass(CouponMapper::class)]
final class CouponMapperTest extends TestCase
{
    private CouponMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new CouponMapper();
    }

    public function testCreateFromData(): void
    {
        $expireTime = new \DateTimeImmutable('2024-12-31 23:59:59');
        $useTime = new \DateTimeImmutable('2024-12-01 12:00:00');

        $data = new CouponData(
            couponCode: 'COUP123456',
            stockId: 'STOCK001',
            openid: 'user_openid_123',
            status: CouponStatus::SENDED,
            expireTime: $expireTime,
            useTime: $useTime
        );

        $coupon = $this->mapper->createFromData($data);

        $this->assertInstanceOf(Coupon::class, $coupon);
        $this->assertSame('COUP123456', $coupon->getCouponCode());
        $this->assertSame('STOCK001', $coupon->getStockId());
        $this->assertSame('user_openid_123', $coupon->getOpenid());
        $this->assertSame(CouponStatus::SENDED, $coupon->getStatus());
        $this->assertEquals($expireTime, $coupon->getExpiryTime());
        $this->assertEquals($useTime, $coupon->getUsedTime());
    }

    public function testCreateFromDataWithNullTimes(): void
    {
        $data = new CouponData(
            couponCode: 'COUP123456',
            stockId: 'STOCK001',
            openid: 'user_openid_123',
            status: CouponStatus::SENDED,
            expireTime: null,
            useTime: null
        );

        $coupon = $this->mapper->createFromData($data);

        $this->assertInstanceOf(Coupon::class, $coupon);
        $this->assertSame('COUP123456', $coupon->getCouponCode());
        $this->assertSame('STOCK001', $coupon->getStockId());
        $this->assertSame('user_openid_123', $coupon->getOpenid());
        $this->assertSame(CouponStatus::SENDED, $coupon->getStatus());
        $this->assertNull($coupon->getExpiryTime());
        $this->assertNull($coupon->getUsedTime());
    }

    public function testUpdateFromData(): void
    {
        $coupon = new Coupon();
        $coupon->setCouponCode('OLD_CODE');
        $coupon->setStatus(CouponStatus::EXPIRED);

        $expireTime = new \DateTimeImmutable('2024-12-31 23:59:59');
        $data = new CouponData(
            couponCode: 'NEW_CODE',
            stockId: 'STOCK002',
            openid: 'new_openid',
            status: CouponStatus::USED,
            expireTime: $expireTime,
            useTime: null
        );

        $this->mapper->updateFromData($coupon, $data);

        $this->assertSame('NEW_CODE', $coupon->getCouponCode());
        $this->assertSame('STOCK002', $coupon->getStockId());
        $this->assertSame('new_openid', $coupon->getOpenid());
        $this->assertSame(CouponStatus::USED, $coupon->getStatus());
        $this->assertEquals($expireTime, $coupon->getExpiryTime());
        $this->assertNull($coupon->getUsedTime());
    }

    /**
     * @param array<string, mixed> $response
     */
    #[DataProvider('provideCreateFromResponseData')]
    public function testCreateFromResponse(array $response, string $openid, string $expectedCouponCode, string $expectedStockId, CouponStatus $expectedStatus): void
    {
        $coupon = $this->mapper->createFromResponse($response, $openid);

        $this->assertInstanceOf(Coupon::class, $coupon);
        $this->assertSame($expectedCouponCode, $coupon->getCouponCode());
        $this->assertSame($expectedStockId, $coupon->getStockId());
        $this->assertSame($openid, $coupon->getOpenid());
        $this->assertSame($expectedStatus, $coupon->getStatus());
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string, string, CouponStatus}>
     */
    public static function provideCreateFromResponseData(): iterable
    {
        yield 'complete_response' => [
            [
                'coupon_code' => 'COUP123456',
                'stock_id' => 'STOCK001',
                'status' => 'SENDED',
                'expire_time' => '2024-12-31 23:59:59',
                'use_time' => '2024-12-01 12:00:00',
            ],
            'user_openid_123',
            'COUP123456',
            'STOCK001',
            CouponStatus::SENDED,
        ];

        yield 'minimal_response' => [
            [],
            'user_openid_456',
            '',
            '',
            CouponStatus::SENDED,
        ];

        yield 'used_status' => [
            [
                'coupon_code' => 'COUP789012',
                'stock_id' => 'STOCK002',
                'status' => 'USED',
            ],
            'user_openid_789',
            'COUP789012',
            'STOCK002',
            CouponStatus::USED,
        ];
    }

    /**
     * @param array<string, mixed> $response
     */
    #[DataProvider('provideUpdateFromResponseData')]
    public function testUpdateFromResponse(array $response, CouponStatus $expectedStatus, bool $shouldHaveExpireTime, bool $shouldHaveUseTime): void
    {
        $coupon = new Coupon();
        $coupon->setStatus(CouponStatus::SENDED);

        $this->mapper->updateFromResponse($coupon, $response);

        $this->assertSame($expectedStatus, $coupon->getStatus());

        if ($shouldHaveExpireTime) {
            $this->assertInstanceOf(\DateTimeImmutable::class, $coupon->getExpiryTime());
        } else {
            $this->assertNull($coupon->getExpiryTime());
        }

        if ($shouldHaveUseTime) {
            $this->assertInstanceOf(\DateTimeImmutable::class, $coupon->getUsedTime());
        } else {
            $this->assertNull($coupon->getUsedTime());
        }
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, CouponStatus, bool, bool}>
     */
    public static function provideUpdateFromResponseData(): iterable
    {
        yield 'status_update_only' => [
            ['status' => 'USED'],
            CouponStatus::USED,
            false, // no expire time
            false, // no use time
        ];

        yield 'with_valid_times' => [
            [
                'status' => 'EXPIRED',
                'expire_time' => '2024-12-31 23:59:59',
                'use_time' => '2024-12-01 12:00:00',
            ],
            CouponStatus::EXPIRED,
            true, // has expire time
            true, // has use time
        ];

        yield 'with_invalid_times' => [
            [
                'status' => 'USED',
                'expire_time' => 'invalid-date',
                'use_time' => 'also-invalid',
            ],
            CouponStatus::USED,
            false, // invalid expire time
            false, // invalid use time
        ];

        yield 'with_timestamp_format' => [
            [
                'status' => 'USED',
                'expire_time' => '@1735689599',
                'use_time' => '@1733054400',
            ],
            CouponStatus::USED,
            true, // valid timestamp
            true, // valid timestamp
        ];

        yield 'no_status_update' => [
            [
                'expire_time' => '2024-12-31 23:59:59',
            ],
            CouponStatus::SENDED, // unchanged
            true, // has expire time
            false, // no use time
        ];
    }

    public function testUpdateFromResponseWithoutStatus(): void
    {
        $coupon = new Coupon();
        $coupon->setStatus(CouponStatus::SENDED);

        $response = [
            'expire_time' => '2024-12-31 23:59:59',
            'other_field' => 'ignored',
        ];

        $this->mapper->updateFromResponse($coupon, $response);

        // Status should remain unchanged when not provided in response
        $this->assertSame(CouponStatus::SENDED, $coupon->getStatus());
        $this->assertInstanceOf(\DateTimeImmutable::class, $coupon->getExpiryTime());
    }

    public function testUpdateFromResponseWithInvalidStatus(): void
    {
        $coupon = new Coupon();
        $coupon->setStatus(CouponStatus::SENDED);

        $response = ['status' => 'INVALID_STATUS'];

        $this->expectException(\ValueError::class);
        $this->mapper->updateFromResponse($coupon, $response);
    }

    public function testUpdateFromResponseWithNonStringTimes(): void
    {
        $coupon = new Coupon();

        $response = [
            'expire_time' => 123456789, // Not a string
            'use_time' => ['not', 'a', 'string'],
        ];

        $this->mapper->updateFromResponse($coupon, $response);

        // Times should remain null when not strings
        $this->assertNull($coupon->getExpiryTime());
        $this->assertNull($coupon->getUsedTime());
    }

    public function testMapDataToCouponIsCalledInBothMethods(): void
    {
        // This is an integration test to ensure both createFromData and updateFromData
        // use the same mapping logic
        $data = new CouponData(
            couponCode: 'TEST_CODE',
            stockId: 'TEST_STOCK',
            openid: 'test_openid',
            status: CouponStatus::USED,
            expireTime: null,
            useTime: null
        );

        // Test createFromData
        $newCoupon = $this->mapper->createFromData($data);

        // Test updateFromData on existing coupon
        $existingCoupon = new Coupon();
        $this->mapper->updateFromData($existingCoupon, $data);

        // Both should have the same values
        $this->assertSame($newCoupon->getCouponCode(), $existingCoupon->getCouponCode());
        $this->assertSame($newCoupon->getStockId(), $existingCoupon->getStockId());
        $this->assertSame($newCoupon->getOpenid(), $existingCoupon->getOpenid());
        $this->assertSame($newCoupon->getStatus(), $existingCoupon->getStatus());
    }
}
