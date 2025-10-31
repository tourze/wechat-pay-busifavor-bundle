<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\DTO;

use WechatPayBusifavorBundle\Enum\CouponStatus;

/**
 * 商家券数据传输对象
 */
class CouponData
{
    public function __construct(
        public readonly string $couponCode,
        public readonly string $stockId,
        public readonly string $openid,
        public readonly CouponStatus $status,
        public readonly ?\DateTimeImmutable $expireTime = null,
        public readonly ?\DateTimeImmutable $useTime = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data, string $openid): self
    {
        $status = self::extractStatus($data);
        $expireTime = self::extractDateTime($data, 'expire_time');
        $useTime = self::extractDateTime($data, 'use_time');

        $couponCode = $data['coupon_code'] ?? '';
        $stockId = $data['stock_id'] ?? '';

        return new self(
            couponCode: is_string($couponCode) ? $couponCode : '',
            stockId: is_string($stockId) ? $stockId : '',
            openid: $openid,
            status: $status,
            expireTime: $expireTime,
            useTime: $useTime,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractStatus(array $data): CouponStatus
    {
        $statusValue = $data['status'] ?? null;

        return (is_int($statusValue) || is_string($statusValue))
            ? CouponStatus::from($statusValue)
            : CouponStatus::SENDED;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractDateTime(array $data, string $key): ?\DateTimeImmutable
    {
        if (!isset($data[$key]) || !is_string($data[$key])) {
            return null;
        }

        $timestamp = strtotime($data[$key]);

        return false !== $timestamp ? new \DateTimeImmutable('@' . $timestamp) : null;
    }
}
