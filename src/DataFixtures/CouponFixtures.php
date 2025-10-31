<?php

namespace WechatPayBusifavorBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Enum\CouponStatus;

class CouponFixtures extends Fixture implements DependentFixtureInterface
{
    public const COUPON_SENDED = 'coupon_sended';
    public const COUPON_USED = 'coupon_used';
    public const COUPON_EXPIRED = 'coupon_expired';

    public function load(ObjectManager $manager): void
    {
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('COUPON001');
        $coupon1->setStockId('STOCK001');
        $coupon1->setOpenid('test_openid_001');
        $coupon1->setStatus(CouponStatus::SENDED);
        $coupon1->setExpiryTime(new \DateTimeImmutable('+30 days'));

        $coupon2 = new Coupon();
        $coupon2->setCouponCode('COUPON002');
        $coupon2->setStockId('STOCK001');
        $coupon2->setOpenid('test_openid_002');
        $coupon2->setStatus(CouponStatus::USED);
        $coupon2->setUsedTime(new \DateTimeImmutable('-1 day'));
        $coupon2->setExpiryTime(new \DateTimeImmutable('+29 days'));
        $coupon2->setTransactionId('TXN123456789');
        $coupon2->setUseRequestNo('REQ123456789');

        $coupon3 = new Coupon();
        $coupon3->setCouponCode('COUPON003');
        $coupon3->setStockId('STOCK002');
        $coupon3->setOpenid('test_openid_003');
        $coupon3->setStatus(CouponStatus::EXPIRED);
        $coupon3->setExpiryTime(new \DateTimeImmutable('-1 day'));

        $manager->persist($coupon1);
        $manager->persist($coupon2);
        $manager->persist($coupon3);

        $manager->flush();

        $this->addReference(self::COUPON_SENDED, $coupon1);
        $this->addReference(self::COUPON_USED, $coupon2);
        $this->addReference(self::COUPON_EXPIRED, $coupon3);
    }

    public function getDependencies(): array
    {
        return [
            StockFixtures::class,
        ];
    }
}
