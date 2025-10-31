<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\DTO;

/**
 * 商家券批次数据传输对象
 */
class StockData
{
    public function __construct(
        public readonly string $stockId,
        public readonly string $stockName,
        public readonly ?string $description = null,
        /** @var array<string, mixed> */
        public readonly array $availableBeginTime = [],
        /** @var array<string, mixed> */
        public readonly array $availableEndTime = [],
        /** @var array<string, mixed> */
        public readonly array $stockUseRule = [],
        /** @var array<string, mixed> */
        public readonly array $couponUseRule = [],
        /** @var array<string, mixed> */
        public readonly array $customEntrance = [],
        /** @var array<string, mixed> */
        public readonly array $displayPatternInfo = [],
        /** @var array<string, mixed>|null */
        public readonly ?array $notifyConfig = null,
        public readonly int $maxCoupons = 0,
        public readonly int $maxCouponsPerUser = 0,
        public readonly int $maxAmount = 0,
        public readonly int $maxAmountByDay = 0,
        public readonly bool $noLimit = false,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $stockUseRuleArray = self::extractArrayOrEmpty($data, 'stock_use_rule');

        return new self(
            stockId: self::extractString($data, 'stock_id'),
            stockName: self::extractString($data, 'stock_name'),
            description: self::extractStringOrNull($data, 'comment'),
            availableBeginTime: self::extractArrayOrEmpty($data, 'available_begin_time'),
            availableEndTime: self::extractArrayOrEmpty($data, 'available_end_time'),
            stockUseRule: $stockUseRuleArray,
            couponUseRule: self::extractArrayOrEmpty($data, 'coupon_use_rule'),
            customEntrance: self::extractArrayOrEmpty($data, 'custom_entrance'),
            displayPatternInfo: self::extractArrayOrEmpty($data, 'display_pattern_info'),
            notifyConfig: self::extractArrayOrNull($data, 'notify_config'),
            maxCoupons: self::extractIntFromRule($stockUseRuleArray, 'max_coupons'),
            maxCouponsPerUser: self::extractIntFromRule($stockUseRuleArray, 'max_coupons_per_user'),
            maxAmount: self::extractIntFromRule($stockUseRuleArray, 'max_amount'),
            maxAmountByDay: self::extractIntFromRule($stockUseRuleArray, 'max_amount_by_day'),
            noLimit: isset($data['no_limit']) && (bool) $data['no_limit'],
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractString(array $data, string $key): string
    {
        $value = $data[$key] ?? '';

        return is_string($value) ? $value : '';
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractStringOrNull(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private static function extractArrayOrEmpty(array $data, string $key): array
    {
        $value = $data[$key] ?? [];

        if (!is_array($value)) {
            return [];
        }

        /** @var array<string, mixed> $value */
        return $value;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    private static function extractArrayOrNull(array $data, string $key): ?array
    {
        if (!isset($data[$key])) {
            return null;
        }

        $value = $data[$key];
        if (!is_array($value)) {
            return null;
        }

        /** @var array<string, mixed> $value */
        return $value;
    }

    /**
     * @param array<string, mixed> $rule
     */
    private static function extractIntFromRule(array $rule, string $key): int
    {
        return isset($rule[$key]) && is_int($rule[$key]) ? $rule[$key] : 0;
    }
}
