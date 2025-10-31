<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Service;

use WechatPayBusifavorBundle\DTO\StockData;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\StockStatus;

/**
 * 商家券批次实体映射器
 */
class StockMapper
{
    public function createFromData(StockData $data): Stock
    {
        $stock = new Stock();
        $this->mapDataToStock($data, $stock);

        return $stock;
    }

    public function updateFromData(Stock $stock, StockData $data): void
    {
        $this->mapDataToStock($data, $stock);
    }

    private function mapDataToStock(StockData $data, Stock $stock): void
    {
        $stock->setStockId($data->stockId);
        $stock->setStockName($data->stockName);
        $stock->setDescription($data->description);
        $stock->setAvailableBeginTime($data->availableBeginTime);
        $stock->setAvailableEndTime($data->availableEndTime);
        $stock->setStockUseRule($this->normalizeStockUseRule($data->stockUseRule));
        $stock->setCouponUseRule($this->normalizeCouponUseRule($data->couponUseRule));
        $stock->setCustomEntrance($this->normalizeCustomEntrance($data->customEntrance));
        $stock->setDisplayPatternInfo($this->normalizeDisplayPatternInfo($data->displayPatternInfo));
        $stock->setNotifyConfig($this->normalizeNotifyConfig($data->notifyConfig));
        $stock->setMaxCoupons($data->maxCoupons);
        $stock->setMaxCouponsPerUser($data->maxCouponsPerUser);
        $stock->setMaxAmount($data->maxAmount);
        $stock->setMaxAmountByDay($data->maxAmountByDay);
        $stock->setNoLimit($data->noLimit);
    }

    /**
     * @param array<string, mixed> $response
     */
    public function updateFromResponse(Stock $stock, array $response): void
    {
        $this->updateStockStatus($stock, $response);
        $this->updateStockBasicInfo($stock, $response);
        $this->updateStockTimeInfo($stock, $response);
        $this->updateStockRules($stock, $response);
    }

    /**
     * @param array<string, mixed> $response
     */
    private function updateStockStatus(Stock $stock, array $response): void
    {
        if (isset($response['stock_state'])) {
            $statusValue = $response['stock_state'];
            if (is_int($statusValue) || is_string($statusValue)) {
                $stock->setStatus(StockStatus::from($statusValue));
            }
        } elseif (isset($response['status'])) {
            $statusValue = $response['status'];
            if (is_int($statusValue) || is_string($statusValue)) {
                $stock->setStatus(StockStatus::from($statusValue));
            }
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    private function updateStockBasicInfo(Stock $stock, array $response): void
    {
        if (isset($response['stock_name']) && is_string($response['stock_name'])) {
            $stock->setStockName($response['stock_name']);
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    private function updateStockTimeInfo(Stock $stock, array $response): void
    {
        if (isset($response['available_begin_time'])) {
            $stock->setAvailableBeginTime(['value' => $response['available_begin_time']]);
        }
        if (isset($response['available_end_time'])) {
            $stock->setAvailableEndTime(['value' => $response['available_end_time']]);
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    private function updateStockRules(Stock $stock, array $response): void
    {
        if (isset($response['stock_use_rule']) && is_array($response['stock_use_rule'])) {
            $stockUseRule = $this->normalizeStockUseRule($response['stock_use_rule']);
            $stock->setStockUseRule($stockUseRule);
        }
        if (isset($response['coupon_use_rule']) && is_array($response['coupon_use_rule'])) {
            $couponUseRule = $this->normalizeCouponUseRule($response['coupon_use_rule']);
            $stock->setCouponUseRule($couponUseRule);
        }
        if (isset($response['custom_entrance']) && is_array($response['custom_entrance'])) {
            $customEntrance = $this->normalizeCustomEntrance($response['custom_entrance']);
            $stock->setCustomEntrance($customEntrance);
        }
        if (isset($response['display_pattern_info']) && is_array($response['display_pattern_info'])) {
            $displayPatternInfo = $this->normalizeDisplayPatternInfo($response['display_pattern_info']);
            $stock->setDisplayPatternInfo($displayPatternInfo);
        }
    }

    /**
     * @param array<mixed, mixed> $data
     * @return array{max_coupons?: int, max_coupons_per_user?: int, max_amount?: int, max_amount_by_day?: int, prevent_api_abuse?: bool, ...}
     */
    private function normalizeStockUseRule(array $data): array
    {
        /** @var array{max_coupons?: int, max_coupons_per_user?: int, max_amount?: int, max_amount_by_day?: int, prevent_api_abuse?: bool, ...} $normalized */
        $normalized = $data;
        return $normalized;
    }

    /**
     * @param array<mixed, mixed> $data
     * @return array{available_merchants?: array<string>, use_limit?: bool, coupon_background?: string, normal_coupon_information?: array<string, mixed>, discount_amount?: int, ...}
     */
    private function normalizeCouponUseRule(array $data): array
    {
        /** @var array{available_merchants?: array<string>, use_limit?: bool, coupon_background?: string, normal_coupon_information?: array<string, mixed>, discount_amount?: int, ...} $normalized */
        $normalized = $data;
        return $normalized;
    }

    /**
     * @param array<mixed, mixed> $data
     * @return array{mini_programs_info?: array<string, mixed>|null, ...}
     */
    private function normalizeCustomEntrance(array $data): array
    {
        /** @var array{mini_programs_info?: array<string, mixed>|null, ...} $normalized */
        $normalized = $data;
        return $normalized;
    }

    /**
     * @param array<mixed, mixed> $data
     * @return array{description?: string, logo_url?: string, background_color?: string, ...}
     */
    private function normalizeDisplayPatternInfo(array $data): array
    {
        /** @var array{description?: string, logo_url?: string, background_color?: string, ...} $normalized */
        $normalized = $data;
        return $normalized;
    }

    /**
     * @param array<mixed, mixed>|null $data
     * @return array{notify_url?: string}|null
     */
    private function normalizeNotifyConfig(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }
        /** @var array{notify_url?: string} $normalized */
        $normalized = $data;
        return $normalized;
    }
}
