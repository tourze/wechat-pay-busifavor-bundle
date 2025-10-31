<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Service;

use WechatPayBusifavorBundle\DTO\CouponData;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Enum\CouponStatus;

/**
 * 商家券实体映射器
 */
class CouponMapper
{
    public function createFromData(CouponData $data): Coupon
    {
        $coupon = new Coupon();
        $this->mapDataToCoupon($data, $coupon);

        return $coupon;
    }

    public function updateFromData(Coupon $coupon, CouponData $data): void
    {
        $this->mapDataToCoupon($data, $coupon);
    }

    private function mapDataToCoupon(CouponData $data, Coupon $coupon): void
    {
        $coupon->setCouponCode($data->couponCode);
        $coupon->setStockId($data->stockId);
        $coupon->setOpenid($data->openid);
        $coupon->setStatus($data->status);

        if (null !== $data->expireTime) {
            $coupon->setExpiryTime($data->expireTime);
        }

        if (null !== $data->useTime) {
            $coupon->setUsedTime($data->useTime);
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    public function createFromResponse(array $response, string $openid): Coupon
    {
        $data = CouponData::fromArray($response, $openid);

        return $this->createFromData($data);
    }

    /**
     * @param array<string, mixed> $response
     */
    public function updateFromResponse(Coupon $coupon, array $response): void
    {
        if (isset($response['status'])) {
            $statusValue = $response['status'];
            if (is_int($statusValue) || is_string($statusValue)) {
                $coupon->setStatus(CouponStatus::from($statusValue));
            }
        }

        $this->updateCouponTimeFromResponse($coupon, $response);
    }

    /**
     * @param array<string, mixed> $response
     */
    private function updateCouponTimeFromResponse(Coupon $coupon, array $response): void
    {
        if (isset($response['expire_time']) && is_string($response['expire_time'])) {
            $timestamp = strtotime($response['expire_time']);
            if (false !== $timestamp) {
                $coupon->setExpiryTime(new \DateTimeImmutable('@' . $timestamp));
            }
        }

        if (isset($response['use_time']) && is_string($response['use_time'])) {
            $timestamp = strtotime($response['use_time']);
            if (false !== $timestamp) {
                $coupon->setUsedTime(new \DateTimeImmutable('@' . $timestamp));
            }
        }
    }
}
