<?php

namespace WechatPayBusifavorBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\StockStatus;

class StockFixtures extends Fixture
{
    public const STOCK_RUNNING = 'stock_running';
    public const STOCK_PAUSED = 'stock_paused';

    public function load(ObjectManager $manager): void
    {
        $stock1 = new Stock();
        $stock1->setStockId('STOCK001');
        $stock1->setStockName('新用户专享代金券');
        $stock1->setDescription('新注册用户专享10元代金券');
        $stock1->setStatus(StockStatus::ONGOING);
        $stock1->setMaxCoupons(1000);
        $stock1->setMaxCouponsPerUser(1);
        $stock1->setMaxAmount(10000);
        $stock1->setMaxAmountByDay(1000);
        $stock1->setRemainAmount(8000);
        $stock1->setDistributedCoupons(200);
        $stock1->setNoLimit(false);
        $stock1->setAvailableBeginTime(['type' => 'fix_time', 'begin_time' => '2024-01-01 00:00:00']);
        $stock1->setAvailableEndTime(['type' => 'fix_time', 'end_time' => '2024-12-31 23:59:59']);
        $stock1->setStockUseRule(['max_coupons' => 1000]);
        $stock1->setCouponUseRule(['normal_coupon_information' => ['amount' => 1000]]);
        $stock1->setCustomEntrance(['mini_programs_info' => ['mini_programs_appid' => 'test_appid']]);
        $stock1->setDisplayPatternInfo(['description' => '新用户专享代金券']);

        $stock2 = new Stock();
        $stock2->setStockId('STOCK002');
        $stock2->setStockName('限时优惠券');
        $stock2->setDescription('限时5元优惠券');
        $stock2->setStatus(StockStatus::PAUSED);
        $stock2->setMaxCoupons(500);
        $stock2->setMaxCouponsPerUser(2);
        $stock2->setMaxAmount(2500);
        $stock2->setMaxAmountByDay(500);
        $stock2->setRemainAmount(2000);
        $stock2->setDistributedCoupons(100);
        $stock2->setNoLimit(false);
        $stock2->setAvailableBeginTime(['type' => 'fix_time', 'begin_time' => '2024-06-01 00:00:00']);
        $stock2->setAvailableEndTime(['type' => 'fix_time', 'end_time' => '2024-06-30 23:59:59']);
        $stock2->setStockUseRule(['max_coupons' => 500]);
        $stock2->setCouponUseRule(['normal_coupon_information' => ['amount' => 500]]);
        $stock2->setCustomEntrance(['mini_programs_info' => ['mini_programs_appid' => 'test_appid']]);
        $stock2->setDisplayPatternInfo(['description' => '限时优惠券']);

        $manager->persist($stock1);
        $manager->persist($stock2);

        $manager->flush();

        $this->addReference(self::STOCK_RUNNING, $stock1);
        $this->addReference(self::STOCK_PAUSED, $stock2);
    }
}
