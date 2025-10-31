<?php

namespace WechatPayBusifavorBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Enum\CouponStatus;

/**
 * @extends ServiceEntityRepository<Coupon>
 */
#[AsRepository(entityClass: Coupon::class)]
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function findByCouponCode(string $couponCode): ?Coupon
    {
        $result = $this->findOneBy(['couponCode' => $couponCode]);

        assert(null === $result || $result instanceof Coupon);

        return $result;
    }

    /** @return array<Coupon> */
    public function findByOpenid(string $openid): array
    {
        $result = $this->createQueryBuilder('c')
            ->where('c.openid = :openid')
            ->setParameter('openid', $openid)
            ->orderBy('c.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        assert(is_array($result));

        /** @var array<Coupon> $result */
        return $result;
    }

    /** @return array<Coupon> */
    public function findByStockId(string $stockId): array
    {
        $result = $this->createQueryBuilder('c')
            ->where('c.stockId = :stockId')
            ->setParameter('stockId', $stockId)
            ->orderBy('c.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        assert(is_array($result));

        /** @var array<Coupon> $result */
        return $result;
    }

    /** @return array<Coupon> */
    public function findAvailableCouponsByOpenid(string $openid): array
    {
        $result = $this->createQueryBuilder('c')
            ->where('c.openid = :openid')
            ->andWhere('c.status = :status')
            ->setParameter('openid', $openid)
            ->setParameter('status', CouponStatus::SENDED)
            ->orderBy('c.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        assert(is_array($result));

        /** @var array<Coupon> $result */
        return $result;
    }

    /** @return array<Coupon> */
    public function findAvailableCouponsByStockId(string $stockId): array
    {
        $result = $this->createQueryBuilder('c')
            ->where('c.stockId = :stockId')
            ->andWhere('c.status = :status')
            ->setParameter('stockId', $stockId)
            ->setParameter('status', CouponStatus::SENDED)
            ->orderBy('c.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        assert(is_array($result));

        /** @var array<Coupon> $result */
        return $result;
    }

    public function countCouponsByStockId(string $stockId): int
    {
        $result = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.stockId = :stockId')
            ->setParameter('stockId', $stockId)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $result;
    }

    public function countAvailableCouponsByStockId(string $stockId): int
    {
        $result = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.stockId = :stockId')
            ->andWhere('c.status = :status')
            ->setParameter('stockId', $stockId)
            ->setParameter('status', CouponStatus::SENDED)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $result;
    }

    public function save(Coupon $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Coupon $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
