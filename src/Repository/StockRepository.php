<?php

namespace WechatPayBusifavorBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\StockStatus;

/**
 * @extends ServiceEntityRepository<Stock>
 */
#[AsRepository(entityClass: Stock::class)]
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function findByStockId(string $stockId): ?Stock
    {
        $result = $this->findOneBy(['stockId' => $stockId]);

        assert(null === $result || $result instanceof Stock);

        return $result;
    }

    /** @return array<Stock> */
    public function findActiveStocks(): array
    {
        $result = $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->setParameter('status', StockStatus::ONGOING)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        assert(is_array($result));

        /** @var array<Stock> $result */
        return $result;
    }

    /** @return array<Stock> */
    public function findStocksByStatus(string $status): array
    {
        $result = $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->setParameter('status', $status)
            ->orderBy('s.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        assert(is_array($result));

        /** @var array<Stock> $result */
        return $result;
    }

    /** @return array<Stock> */
    public function findStocksByStatusEnum(StockStatus $status): array
    {
        return $this->findStocksByStatus($status->value);
    }

    public function save(Stock $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Stock $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
