<?php

namespace WechatPayBusifavorBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\StockStatus;

/**
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function findByStockId(string $stockId): ?Stock
    {
        return $this->findOneBy(['stockId' => $stockId]);
    }

    public function findActiveStocks(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->setParameter('status', StockStatus::ONGOING)
            ->orderBy('s.createdTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findStocksByStatus(string $status): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->setParameter('status', $status)
            ->orderBy('s.createdTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findStocksByStatusEnum(StockStatus $status): array
    {
        return $this->findStocksByStatus($status->value);
    }
}
