<?php

namespace WechatPayBusifavorBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Enum\CouponStatus;

/**
 * @method Coupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coupon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coupon[]    findAll()
 * @method Coupon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function findByCouponCode(string $couponCode): ?Coupon
    {
        return $this->findOneBy(['couponCode' => $couponCode]);
    }

    public function findByOpenid(string $openid): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.openid = :openid')
            ->setParameter('openid', $openid)
            ->orderBy('c.createdTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStockId(string $stockId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.stockId = :stockId')
            ->setParameter('stockId', $stockId)
            ->orderBy('c.createdTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAvailableCouponsByOpenid(string $openid): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.openid = :openid')
            ->andWhere('c.status = :status')
            ->setParameter('openid', $openid)
            ->setParameter('status', CouponStatus::SENDED)
            ->orderBy('c.createdTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAvailableCouponsByStockId(string $stockId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.stockId = :stockId')
            ->andWhere('c.status = :status')
            ->setParameter('stockId', $stockId)
            ->setParameter('status', CouponStatus::SENDED)
            ->orderBy('c.createdTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countCouponsByStockId(string $stockId): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.stockId = :stockId')
            ->setParameter('stockId', $stockId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countAvailableCouponsByStockId(string $stockId): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.stockId = :stockId')
            ->andWhere('c.status = :status')
            ->setParameter('stockId', $stockId)
            ->setParameter('status', CouponStatus::SENDED)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
