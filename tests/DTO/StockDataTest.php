<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\DTO\StockData;

/**
 * @internal
 */
#[CoversClass(StockData::class)]
final class StockDataTest extends TestCase
{
    public function testConstruct(): void
    {
        $availableBeginTime = ['value' => '2024-01-01 00:00:00'];
        $availableEndTime = ['value' => '2024-12-31 23:59:59'];
        $stockUseRule = ['max_coupons' => 1000, 'max_coupons_per_user' => 5];
        $couponUseRule = ['available_time' => 'weekday'];
        $customEntrance = ['mini_programs_info' => []];
        $displayPatternInfo = ['description' => 'Test coupon'];
        $notifyConfig = ['notify_appid' => 'wx123456'];

        $stockData = new StockData(
            stockId: 'STOCK001',
            stockName: 'Test Stock',
            description: 'Test description',
            availableBeginTime: $availableBeginTime,
            availableEndTime: $availableEndTime,
            stockUseRule: $stockUseRule,
            couponUseRule: $couponUseRule,
            customEntrance: $customEntrance,
            displayPatternInfo: $displayPatternInfo,
            notifyConfig: $notifyConfig,
            maxCoupons: 1000,
            maxCouponsPerUser: 5,
            maxAmount: 50000,
            maxAmountByDay: 10000,
            noLimit: false
        );

        $this->assertSame('STOCK001', $stockData->stockId);
        $this->assertSame('Test Stock', $stockData->stockName);
        $this->assertSame('Test description', $stockData->description);
        $this->assertSame($availableBeginTime, $stockData->availableBeginTime);
        $this->assertSame($availableEndTime, $stockData->availableEndTime);
        $this->assertSame($stockUseRule, $stockData->stockUseRule);
        $this->assertSame($couponUseRule, $stockData->couponUseRule);
        $this->assertSame($customEntrance, $stockData->customEntrance);
        $this->assertSame($displayPatternInfo, $stockData->displayPatternInfo);
        $this->assertSame($notifyConfig, $stockData->notifyConfig);
        $this->assertSame(1000, $stockData->maxCoupons);
        $this->assertSame(5, $stockData->maxCouponsPerUser);
        $this->assertSame(50000, $stockData->maxAmount);
        $this->assertSame(10000, $stockData->maxAmountByDay);
        $this->assertFalse($stockData->noLimit);
    }

    public function testConstructWithDefaults(): void
    {
        $stockData = new StockData(
            stockId: 'STOCK002',
            stockName: 'Minimal Stock'
        );

        $this->assertSame('STOCK002', $stockData->stockId);
        $this->assertSame('Minimal Stock', $stockData->stockName);
        $this->assertNull($stockData->description);
        $this->assertSame([], $stockData->availableBeginTime);
        $this->assertSame([], $stockData->availableEndTime);
        $this->assertSame([], $stockData->stockUseRule);
        $this->assertSame([], $stockData->couponUseRule);
        $this->assertSame([], $stockData->customEntrance);
        $this->assertSame([], $stockData->displayPatternInfo);
        $this->assertNull($stockData->notifyConfig);
        $this->assertSame(0, $stockData->maxCoupons);
        $this->assertSame(0, $stockData->maxCouponsPerUser);
        $this->assertSame(0, $stockData->maxAmount);
        $this->assertSame(0, $stockData->maxAmountByDay);
        $this->assertFalse($stockData->noLimit);
    }

    /**
     * @param array<string, mixed> $inputData
     */
    #[DataProvider('provideFromArrayData')]
    public function testFromArray(array $inputData, StockData $expected): void
    {
        $result = StockData::fromArray($inputData);

        $this->assertSame($expected->stockId, $result->stockId);
        $this->assertSame($expected->stockName, $result->stockName);
        $this->assertSame($expected->description, $result->description);
        $this->assertSame($expected->availableBeginTime, $result->availableBeginTime);
        $this->assertSame($expected->availableEndTime, $result->availableEndTime);
        $this->assertSame($expected->stockUseRule, $result->stockUseRule);
        $this->assertSame($expected->couponUseRule, $result->couponUseRule);
        $this->assertSame($expected->customEntrance, $result->customEntrance);
        $this->assertSame($expected->displayPatternInfo, $result->displayPatternInfo);
        $this->assertSame($expected->notifyConfig, $result->notifyConfig);
        $this->assertSame($expected->maxCoupons, $result->maxCoupons);
        $this->assertSame($expected->maxCouponsPerUser, $result->maxCouponsPerUser);
        $this->assertSame($expected->maxAmount, $result->maxAmount);
        $this->assertSame($expected->maxAmountByDay, $result->maxAmountByDay);
        $this->assertSame($expected->noLimit, $result->noLimit);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, StockData}>
     */
    public static function provideFromArrayData(): iterable
    {
        yield 'complete_data' => [
            [
                'stock_id' => 'STOCK001',
                'stock_name' => 'Complete Stock',
                'comment' => 'Full description',
                'available_begin_time' => ['value' => '2024-01-01 00:00:00'],
                'available_end_time' => ['value' => '2024-12-31 23:59:59'],
                'stock_use_rule' => [
                    'max_coupons' => 1000,
                    'max_coupons_per_user' => 5,
                    'max_amount' => 50000,
                    'max_amount_by_day' => 10000,
                ],
                'coupon_use_rule' => ['available_time' => 'weekday'],
                'custom_entrance' => ['mini_programs_info' => []],
                'display_pattern_info' => ['description' => 'Pattern info'],
                'notify_config' => ['notify_appid' => 'wx123456'],
                'no_limit' => true,
            ],
            new StockData(
                stockId: 'STOCK001',
                stockName: 'Complete Stock',
                description: 'Full description',
                availableBeginTime: ['value' => '2024-01-01 00:00:00'],
                availableEndTime: ['value' => '2024-12-31 23:59:59'],
                stockUseRule: [
                    'max_coupons' => 1000,
                    'max_coupons_per_user' => 5,
                    'max_amount' => 50000,
                    'max_amount_by_day' => 10000,
                ],
                couponUseRule: ['available_time' => 'weekday'],
                customEntrance: ['mini_programs_info' => []],
                displayPatternInfo: ['description' => 'Pattern info'],
                notifyConfig: ['notify_appid' => 'wx123456'],
                maxCoupons: 1000,
                maxCouponsPerUser: 5,
                maxAmount: 50000,
                maxAmountByDay: 10000,
                noLimit: true
            ),
        ];

        yield 'minimal_data' => [
            [],
            new StockData(
                stockId: '',
                stockName: '',
                description: null,
                availableBeginTime: [],
                availableEndTime: [],
                stockUseRule: [],
                couponUseRule: [],
                customEntrance: [],
                displayPatternInfo: [],
                notifyConfig: null,
                maxCoupons: 0,
                maxCouponsPerUser: 0,
                maxAmount: 0,
                maxAmountByDay: 0,
                noLimit: false
            ),
        ];

        yield 'partial_data' => [
            [
                'stock_id' => 'STOCK002',
                'stock_name' => 'Partial Stock',
                'stock_use_rule' => [
                    'max_coupons' => 500,
                ],
            ],
            new StockData(
                stockId: 'STOCK002',
                stockName: 'Partial Stock',
                description: null,
                availableBeginTime: [],
                availableEndTime: [],
                stockUseRule: ['max_coupons' => 500],
                couponUseRule: [],
                customEntrance: [],
                displayPatternInfo: [],
                notifyConfig: null,
                maxCoupons: 500,
                maxCouponsPerUser: 0,
                maxAmount: 0,
                maxAmountByDay: 0,
                noLimit: false
            ),
        ];

        yield 'with_false_no_limit' => [
            [
                'stock_id' => 'STOCK003',
                'stock_name' => 'Limited Stock',
                'no_limit' => false,
            ],
            new StockData(
                stockId: 'STOCK003',
                stockName: 'Limited Stock',
                description: null,
                availableBeginTime: [],
                availableEndTime: [],
                stockUseRule: [],
                couponUseRule: [],
                customEntrance: [],
                displayPatternInfo: [],
                notifyConfig: null,
                maxCoupons: 0,
                maxCouponsPerUser: 0,
                maxAmount: 0,
                maxAmountByDay: 0,
                noLimit: false
            ),
        ];

        yield 'with_zero_no_limit' => [
            [
                'stock_id' => 'STOCK004',
                'stock_name' => 'Zero No Limit Stock',
                'no_limit' => 0,
            ],
            new StockData(
                stockId: 'STOCK004',
                stockName: 'Zero No Limit Stock',
                description: null,
                availableBeginTime: [],
                availableEndTime: [],
                stockUseRule: [],
                couponUseRule: [],
                customEntrance: [],
                displayPatternInfo: [],
                notifyConfig: null,
                maxCoupons: 0,
                maxCouponsPerUser: 0,
                maxAmount: 0,
                maxAmountByDay: 0,
                noLimit: false
            ),
        ];
    }

    public function testFromArrayExtractsStockUseRuleValues(): void
    {
        $data = [
            'stock_use_rule' => [
                'max_coupons' => 2000,
                'max_coupons_per_user' => 10,
                'max_amount' => 100000,
                'max_amount_by_day' => 20000,
                'other_field' => 'ignored',
            ],
        ];

        $result = StockData::fromArray($data);

        $this->assertSame(2000, $result->maxCoupons);
        $this->assertSame(10, $result->maxCouponsPerUser);
        $this->assertSame(100000, $result->maxAmount);
        $this->assertSame(20000, $result->maxAmountByDay);
    }

    public function testFromArrayWithNullNotifyConfig(): void
    {
        $data = [
            'notify_config' => null,
        ];

        $result = StockData::fromArray($data);

        $this->assertNull($result->notifyConfig);
    }

    public function testFromArrayWithEmptyArrayNotifyConfig(): void
    {
        $data = [
            'notify_config' => [],
        ];

        $result = StockData::fromArray($data);

        $this->assertSame([], $result->notifyConfig);
    }
}
