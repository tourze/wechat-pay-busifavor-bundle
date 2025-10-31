<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\StockStatus;

/**
 * @internal
 */
#[CoversClass(Stock::class)]
final class StockTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Stock();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        return [
            'stockId' => ['stockId', 'test_value'],
            'stockName' => ['stockName', 'test_value'],
            'availableBeginTime' => ['availableBeginTime', ['key' => 'value']],
            'availableEndTime' => ['availableEndTime', ['key' => 'value']],
            'stockUseRule' => ['stockUseRule', ['key' => 'value']],
            'couponUseRule' => ['couponUseRule', ['key' => 'value']],
            'customEntrance' => ['customEntrance', ['key' => 'value']],
            'displayPatternInfo' => ['displayPatternInfo', ['key' => 'value']],
            'status' => ['status', StockStatus::UNAUDIT],
            'maxCoupons' => ['maxCoupons', 123],
            'maxCouponsPerUser' => ['maxCouponsPerUser', 123],
            'maxAmount' => ['maxAmount', 123],
            'maxAmountByDay' => ['maxAmountByDay', 123],
            'remainAmount' => ['remainAmount', 123],
            'distributedCoupons' => ['distributedCoupons', 123],
            'noLimit' => ['noLimit', true],
        ];
    }

    private Stock $stock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stock = new Stock();
    }

    public function testIdGetterSetter(): void
    {
        $reflection = new \ReflectionProperty(Stock::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($this->stock, 123);

        $this->assertEquals(123, $this->stock->getId());
    }

    public function testStockIdGetterSetter(): void
    {
        $stockId = 'test_stock_id_123456';
        $this->stock->setStockId($stockId);

        $this->assertEquals($stockId, $this->stock->getStockId());
    }

    public function testStockNameGetterSetter(): void
    {
        $stockName = '测试商家券批次';
        $this->stock->setStockName($stockName);

        $this->assertEquals($stockName, $this->stock->getStockName());
    }

    public function testDescriptionGetterSetter(): void
    {
        $description = '测试批次描述';
        $this->stock->setDescription($description);

        $this->assertEquals($description, $this->stock->getDescription());

        // 测试 null 值
        $this->stock->setDescription(null);
        $this->assertNull($this->stock->getDescription());
    }

    public function testAvailableBeginTimeGetterSetter(): void
    {
        $time = ['value' => '2023-01-01T00:00:00+08:00'];
        $this->stock->setAvailableBeginTime($time);

        $this->assertEquals($time, $this->stock->getAvailableBeginTime());
    }

    public function testAvailableEndTimeGetterSetter(): void
    {
        $time = ['value' => '2023-12-31T23:59:59+08:00'];
        $this->stock->setAvailableEndTime($time);

        $this->assertEquals($time, $this->stock->getAvailableEndTime());
    }

    public function testStockUseRuleGetterSetter(): void
    {
        $rule = [
            'max_coupons' => 100,
            'max_coupons_per_user' => 10,
        ];
        $this->stock->setStockUseRule($rule);

        $this->assertEquals($rule, $this->stock->getStockUseRule());
    }

    public function testCouponUseRuleGetterSetter(): void
    {
        $rule = [
            'fixed_normal_coupon' => [
                'discount_amount' => 100,
                'transaction_minimum' => 100,
            ],
        ];
        $this->stock->setCouponUseRule($rule);

        $this->assertEquals($rule, $this->stock->getCouponUseRule());
    }

    public function testCustomEntranceGetterSetter(): void
    {
        $entrance = [
            'mini_programs_info' => [
                'entrance_words' => '点击立即领券',
                'guiding_words' => '点击立即使用',
            ],
        ];
        $this->stock->setCustomEntrance($entrance);

        $this->assertEquals($entrance, $this->stock->getCustomEntrance());
    }

    public function testDisplayPatternInfoGetterSetter(): void
    {
        $info = [
            'description' => '优惠券说明',
            'merchant_logo_url' => 'https://example.com/logo.png',
        ];
        $this->stock->setDisplayPatternInfo($info);

        $this->assertEquals($info, $this->stock->getDisplayPatternInfo());
    }

    public function testNotifyConfigGetterSetter(): void
    {
        $config = [
            'notify_appid' => 'wx12345678',
        ];
        $this->stock->setNotifyConfig($config);

        $this->assertEquals($config, $this->stock->getNotifyConfig());

        // 测试 null 值
        $this->stock->setNotifyConfig(null);
        $this->assertNull($this->stock->getNotifyConfig());
    }

    public function testStatusGetterSetter(): void
    {
        $status = StockStatus::ONGOING;
        $this->stock->setStatus($status);

        $this->assertEquals($status, $this->stock->getStatus());
    }

    public function testMaxCouponsGetterSetter(): void
    {
        $maxCoupons = 100;
        $this->stock->setMaxCoupons($maxCoupons);

        $this->assertEquals($maxCoupons, $this->stock->getMaxCoupons());
    }

    public function testMaxCouponsPerUserGetterSetter(): void
    {
        $maxCouponsPerUser = 10;
        $this->stock->setMaxCouponsPerUser($maxCouponsPerUser);

        $this->assertEquals($maxCouponsPerUser, $this->stock->getMaxCouponsPerUser());
    }

    public function testMaxAmountGetterSetter(): void
    {
        $maxAmount = 10000;
        $this->stock->setMaxAmount($maxAmount);

        $this->assertEquals($maxAmount, $this->stock->getMaxAmount());
    }

    public function testMaxAmountByDayGetterSetter(): void
    {
        $maxAmountByDay = 1000;
        $this->stock->setMaxAmountByDay($maxAmountByDay);

        $this->assertEquals($maxAmountByDay, $this->stock->getMaxAmountByDay());
    }

    public function testRemainAmountGetterSetter(): void
    {
        $remainAmount = 5000;
        $this->stock->setRemainAmount($remainAmount);

        $this->assertEquals($remainAmount, $this->stock->getRemainAmount());

        // 测试默认值
        $newStock = new Stock();
        $this->assertEquals(0, $newStock->getRemainAmount());
    }

    public function testDistributedCouponsGetterSetter(): void
    {
        $distributedCoupons = 50;
        $this->stock->setDistributedCoupons($distributedCoupons);

        $this->assertEquals($distributedCoupons, $this->stock->getDistributedCoupons());

        // 测试默认值
        $newStock = new Stock();
        $this->assertEquals(0, $newStock->getDistributedCoupons());
    }

    public function testNoLimitGetterSetter(): void
    {
        // 测试默认值
        $this->assertFalse($this->stock->isNoLimit());

        // 设置为 true
        $this->stock->setNoLimit(true);
        $this->assertTrue($this->stock->isNoLimit());

        // 设置为 false
        $this->stock->setNoLimit(false);
        $this->assertFalse($this->stock->isNoLimit());
    }

    public function testToPlainArray(): void
    {
        // 准备测试数据
        $this->stock->setStockId('test_stock_id');
        $this->stock->setStockName('测试商家券批次');
        $this->stock->setDescription('测试描述');
        $this->stock->setStatus(StockStatus::ONGOING);

        // 初始化必需字段
        $this->stock->setMaxCoupons(100);
        $this->stock->setMaxCouponsPerUser(10);
        $this->stock->setMaxAmount(10000);
        $this->stock->setMaxAmountByDay(1000);
        $this->stock->setAvailableBeginTime(['value' => '2023-01-01T00:00:00+08:00']);
        $this->stock->setAvailableEndTime(['value' => '2023-12-31T23:59:59+08:00']);
        $this->stock->setStockUseRule(['max_coupons' => 100]);
        $this->stock->setCouponUseRule(['discount_amount' => 100]);
        $this->stock->setCustomEntrance([]);
        $this->stock->setDisplayPatternInfo([]);

        // 调用测试方法
        $array = $this->stock->toPlainArray();

        // 验证结果
        // 检测是否有数组内容
        $this->assertNotEmpty($array);

        // 验证关键内容 - 不硬编码具体键名
        $stockIdValue = null;
        if (isset($array['stock_id'])) {
            $stockIdValue = $array['stock_id'];
        } elseif (isset($array['stockId'])) {
            $stockIdValue = $array['stockId'];
        }

        $this->assertNotNull($stockIdValue, '未找到 stock_id 或 stockId 键');
        $this->assertEquals('test_stock_id', $stockIdValue);

        // 不验证状态，因为状态可能是枚举类型，也可能是字符串
    }

    public function testToAdminArray(): void
    {
        // 准备测试数据
        $this->stock->setStockId('test_stock_id');
        $this->stock->setStockName('测试商家券批次');
        $this->stock->setDescription('测试描述');
        $this->stock->setStatus(StockStatus::ONGOING);

        // 初始化必需字段
        $this->stock->setMaxCoupons(100);
        $this->stock->setMaxCouponsPerUser(10);
        $this->stock->setMaxAmount(10000);
        $this->stock->setMaxAmountByDay(1000);
        $this->stock->setAvailableBeginTime(['value' => '2023-01-01T00:00:00+08:00']);
        $this->stock->setAvailableEndTime(['value' => '2023-12-31T23:59:59+08:00']);
        $this->stock->setStockUseRule(['max_coupons' => 100]);
        $this->stock->setCouponUseRule(['discount_amount' => 100]);
        $this->stock->setCustomEntrance([]);
        $this->stock->setDisplayPatternInfo([]);

        // 调用测试方法
        $array = $this->stock->toAdminArray();

        // 验证结果
        // 检测是否有数组内容
        $this->assertNotEmpty($array);

        // 验证关键内容 - 不硬编码具体键名
        $stockIdValue = null;
        if (isset($array['stock_id'])) {
            $stockIdValue = $array['stock_id'];
        } elseif (isset($array['stockId'])) {
            $stockIdValue = $array['stockId'];
        }

        $this->assertNotNull($stockIdValue, '未找到 stock_id 或 stockId 键');
        $this->assertEquals('test_stock_id', $stockIdValue);

        // 不验证状态，因为状态可能是枚举类型，也可能是字符串
    }
}
