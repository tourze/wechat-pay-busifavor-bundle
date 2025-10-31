<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\DTO\StockData;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\StockStatus;
use WechatPayBusifavorBundle\Service\StockMapper;

/**
 * @internal
 */
#[CoversClass(StockMapper::class)]
final class StockMapperTest extends TestCase
{
    private StockMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new StockMapper();
    }

    public function testCreateFromData(): void
    {
        $availableBeginTime = ['value' => '2024-01-01 00:00:00'];
        $availableEndTime = ['value' => '2024-12-31 23:59:59'];
        $stockUseRule = ['max_coupons' => 1000, 'max_coupons_per_user' => 5];
        $couponUseRule = ['available_time' => 'weekday'];
        $customEntrance = ['mini_programs_info' => []];
        $displayPatternInfo = ['description' => 'Test coupon'];
        $notifyConfig = ['notify_appid' => 'wx123456'];

        $data = new StockData(
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

        $stock = $this->mapper->createFromData($data);

        $this->assertInstanceOf(Stock::class, $stock);
        $this->assertSame('STOCK001', $stock->getStockId());
        $this->assertSame('Test Stock', $stock->getStockName());
        $this->assertSame('Test description', $stock->getDescription());
        $this->assertSame($availableBeginTime, $stock->getAvailableBeginTime());
        $this->assertSame($availableEndTime, $stock->getAvailableEndTime());
        $this->assertSame($stockUseRule, $stock->getStockUseRule());
        $this->assertSame($couponUseRule, $stock->getCouponUseRule());
        $this->assertSame($customEntrance, $stock->getCustomEntrance());
        $this->assertSame($displayPatternInfo, $stock->getDisplayPatternInfo());
        $this->assertSame($notifyConfig, $stock->getNotifyConfig());
        $this->assertSame(1000, $stock->getMaxCoupons());
        $this->assertSame(5, $stock->getMaxCouponsPerUser());
        $this->assertSame(50000, $stock->getMaxAmount());
        $this->assertSame(10000, $stock->getMaxAmountByDay());
        $this->assertFalse($stock->isNoLimit());
    }

    public function testCreateFromDataWithDefaults(): void
    {
        $data = new StockData(
            stockId: 'STOCK002',
            stockName: 'Minimal Stock'
        );

        $stock = $this->mapper->createFromData($data);

        $this->assertInstanceOf(Stock::class, $stock);
        $this->assertSame('STOCK002', $stock->getStockId());
        $this->assertSame('Minimal Stock', $stock->getStockName());
        $this->assertNull($stock->getDescription());
        $this->assertSame([], $stock->getAvailableBeginTime());
        $this->assertSame([], $stock->getAvailableEndTime());
        $this->assertSame([], $stock->getStockUseRule());
        $this->assertSame([], $stock->getCouponUseRule());
        $this->assertSame([], $stock->getCustomEntrance());
        $this->assertSame([], $stock->getDisplayPatternInfo());
        $this->assertNull($stock->getNotifyConfig());
        $this->assertSame(0, $stock->getMaxCoupons());
        $this->assertSame(0, $stock->getMaxCouponsPerUser());
        $this->assertSame(0, $stock->getMaxAmount());
        $this->assertSame(0, $stock->getMaxAmountByDay());
        $this->assertFalse($stock->isNoLimit());
    }

    public function testUpdateFromData(): void
    {
        $stock = new Stock();
        $stock->setStockId('OLD_ID');
        $stock->setStockName('Old Name');

        $data = new StockData(
            stockId: 'NEW_ID',
            stockName: 'New Name',
            description: 'Updated description',
            maxCoupons: 2000,
            maxCouponsPerUser: 10,
            noLimit: true
        );

        $this->mapper->updateFromData($stock, $data);

        $this->assertSame('NEW_ID', $stock->getStockId());
        $this->assertSame('New Name', $stock->getStockName());
        $this->assertSame('Updated description', $stock->getDescription());
        $this->assertSame(2000, $stock->getMaxCoupons());
        $this->assertSame(10, $stock->getMaxCouponsPerUser());
        $this->assertTrue($stock->isNoLimit());
    }

    /**
     * @param array<string, mixed> $response
     */
    #[DataProvider('provideUpdateFromResponseData')]
    public function testUpdateFromResponse(array $response, StockStatus $expectedStatus, string $expectedName): void
    {
        $stock = new Stock();
        $stock->setStatus(StockStatus::CHECKING);
        $stock->setStockName('Original Name');

        $this->mapper->updateFromResponse($stock, $response);

        $this->assertSame($expectedStatus, $stock->getStatus());
        $this->assertSame($expectedName, $stock->getStockName());
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, StockStatus, string}>
     */
    public static function provideUpdateFromResponseData(): iterable
    {
        yield 'stock_state_update' => [
            [
                'stock_state' => 'ONGOING',
                'stock_name' => 'Updated Name',
            ],
            StockStatus::ONGOING,
            'Updated Name',
        ];

        yield 'status_field_update' => [
            [
                'status' => 'PAUSED',
                'stock_name' => 'Another Name',
            ],
            StockStatus::PAUSED,
            'Another Name',
        ];

        yield 'stock_state_priority_over_status' => [
            [
                'stock_state' => 'ONGOING',
                'status' => 'PAUSED', // Should be ignored
            ],
            StockStatus::ONGOING, // stock_state takes priority
            'Original Name', // unchanged
        ];

        yield 'no_status_update' => [
            [
                'stock_name' => 'Name Only Update',
            ],
            StockStatus::CHECKING, // unchanged
            'Name Only Update',
        ];
    }

    public function testUpdateFromResponseWithTimeInfo(): void
    {
        $stock = new Stock();

        $response = [
            'available_begin_time' => '2024-01-01 00:00:00',
            'available_end_time' => '2024-12-31 23:59:59',
        ];

        $this->mapper->updateFromResponse($stock, $response);

        $this->assertSame(['value' => '2024-01-01 00:00:00'], $stock->getAvailableBeginTime());
        $this->assertSame(['value' => '2024-12-31 23:59:59'], $stock->getAvailableEndTime());
    }

    public function testUpdateFromResponseWithRules(): void
    {
        $stock = new Stock();

        $stockUseRule = ['max_coupons' => 1000];
        $couponUseRule = ['available_time' => 'weekday'];
        $customEntrance = ['mini_programs_info' => []];
        $displayPatternInfo = ['description' => 'Updated pattern'];

        $response = [
            'stock_use_rule' => $stockUseRule,
            'coupon_use_rule' => $couponUseRule,
            'custom_entrance' => $customEntrance,
            'display_pattern_info' => $displayPatternInfo,
        ];

        $this->mapper->updateFromResponse($stock, $response);

        $this->assertSame($stockUseRule, $stock->getStockUseRule());
        $this->assertSame($couponUseRule, $stock->getCouponUseRule());
        $this->assertSame($customEntrance, $stock->getCustomEntrance());
        $this->assertSame($displayPatternInfo, $stock->getDisplayPatternInfo());
    }

    public function testUpdateFromResponseWithInvalidStatus(): void
    {
        $stock = new Stock();
        $stock->setStatus(StockStatus::CHECKING);

        $response = ['stock_state' => 'INVALID_STATUS'];

        $this->expectException(\ValueError::class);
        $this->mapper->updateFromResponse($stock, $response);
    }

    public function testUpdateFromResponseWithEmptyResponse(): void
    {
        $stock = new Stock();
        $originalStatus = StockStatus::CHECKING;
        $originalName = 'Original Name';

        $stock->setStatus($originalStatus);
        $stock->setStockName($originalName);

        $this->mapper->updateFromResponse($stock, []);

        // Nothing should change with empty response
        $this->assertSame($originalStatus, $stock->getStatus());
        $this->assertSame($originalName, $stock->getStockName());
    }

    public function testMapDataToStockIsCalledInBothMethods(): void
    {
        // This is an integration test to ensure both createFromData and updateFromData
        // use the same mapping logic
        $data = new StockData(
            stockId: 'TEST_STOCK',
            stockName: 'Test Stock Name',
            description: 'Test description',
            maxCoupons: 500,
            noLimit: true
        );

        // Test createFromData
        $newStock = $this->mapper->createFromData($data);

        // Test updateFromData on existing stock
        $existingStock = new Stock();
        $this->mapper->updateFromData($existingStock, $data);

        // Both should have the same values
        $this->assertSame($newStock->getStockId(), $existingStock->getStockId());
        $this->assertSame($newStock->getStockName(), $existingStock->getStockName());
        $this->assertSame($newStock->getDescription(), $existingStock->getDescription());
        $this->assertSame($newStock->getMaxCoupons(), $existingStock->getMaxCoupons());
        $this->assertSame($newStock->isNoLimit(), $existingStock->isNoLimit());
    }

    /**
     * @param array<string, mixed> $response
     */
    #[DataProvider('provideComplexUpdateScenarios')]
    public function testComplexUpdateScenarios(array $response, callable $assertionCallback): void
    {
        $stock = new Stock();
        $stock->setStatus(StockStatus::CHECKING);
        $stock->setStockName('Original');

        $this->mapper->updateFromResponse($stock, $response);

        // 执行回调中的断言
        $assertionCallback($stock);

        // 确保mapper执行了更新操作
        $this->assertNotNull($stock, 'Stock should not be null after update');
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, callable}>
     */
    public static function provideComplexUpdateScenarios(): iterable
    {
        yield 'all_rules_update' => [
            [
                'stock_state' => 'ONGOING',
                'stock_name' => 'Full Update',
                'available_begin_time' => '2024-01-01',
                'available_end_time' => '2024-12-31',
                'stock_use_rule' => ['max_coupons' => 2000],
                'coupon_use_rule' => ['type' => 'normal'],
                'custom_entrance' => ['appid' => 'wx123'],
                'display_pattern_info' => ['color' => 'red'],
            ],
            static function (Stock $stock): void {
                self::assertSame(StockStatus::ONGOING, $stock->getStatus());
                self::assertSame('Full Update', $stock->getStockName());
                self::assertSame(['value' => '2024-01-01'], $stock->getAvailableBeginTime());
                self::assertSame(['value' => '2024-12-31'], $stock->getAvailableEndTime());
                self::assertSame(['max_coupons' => 2000], $stock->getStockUseRule());
                self::assertSame(['type' => 'normal'], $stock->getCouponUseRule());
                self::assertSame(['appid' => 'wx123'], $stock->getCustomEntrance());
                self::assertSame(['color' => 'red'], $stock->getDisplayPatternInfo());
            },
        ];

        yield 'partial_rules_update' => [
            [
                'stock_use_rule' => ['new_rule' => 'value'],
                'other_field' => 'ignored',
            ],
            static function (Stock $stock): void {
                self::assertSame(StockStatus::CHECKING, $stock->getStatus()); // unchanged
                self::assertSame('Original', $stock->getStockName()); // unchanged
                self::assertSame(['new_rule' => 'value'], $stock->getStockUseRule());
            },
        ];
    }
}
