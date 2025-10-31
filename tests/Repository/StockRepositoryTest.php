<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\StockStatus;
use WechatPayBusifavorBundle\Repository\StockRepository;

/**
 * @template-extends AbstractRepositoryTestCase<Stock>
 * @internal
 */
#[CoversClass(StockRepository::class)]
#[RunTestsInSeparateProcesses]
final class StockRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 如果当前测试是数据库连接测试，跳过数据加载操作
        if ($this->isTestingDatabaseConnection()) {
            return;
        }

        // 清理实体管理器状态，避免影响数据库连接测试
        try {
            self::getEntityManager()->clear();
        } catch (\Exception $e) {
            // 忽略清理错误
        }
    }

    /**
     * 测试可以获取仓库实例
     */
    public function testCanGetRepositoryInstance(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);
        $this->assertInstanceOf(StockRepository::class, $repository);
    }

    /**
     * 测试仓库定义了必要的方法
     */
    public function testRepositoryHasRequiredMethods(): void
    {
        $methods = get_class_methods(StockRepository::class);

        $requiredMethods = [
            'findByStockId',
            'findActiveStocks',
            'findStocksByStatus',
            'findStocksByStatusEnum',
        ];

        foreach ($requiredMethods as $method) {
            $this->assertContains($method, $methods, "Method {$method} should exist in StockRepository");
        }
    }

    public function testFindActiveStocks(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);
        $this->assertInstanceOf(StockRepository::class, $repository);
        $result = $repository->findActiveStocks();
        $this->assertIsArray($result);
    }

    public function testFindByStockId(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);
        $this->assertInstanceOf(StockRepository::class, $repository);
        $result = $repository->findByStockId('test-stock');
        $this->assertNull($result);
    }

    public function testFindStocksByStatus(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);
        $this->assertInstanceOf(StockRepository::class, $repository);
        $result = $repository->findStocksByStatus('ONGOING');
        $this->assertIsArray($result);
    }

    public function testFindStocksByStatusEnum(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);
        $this->assertInstanceOf(StockRepository::class, $repository);
        $result = $repository->findStocksByStatusEnum(StockStatus::ONGOING);
        $this->assertIsArray($result);
    }

    public function testSave(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);
        $this->assertInstanceOf(StockRepository::class, $repository);

        $stock = $this->createStock();
        $stock->setStockId('test-stock');

        $repository->save($stock, false);
        $this->assertTrue(self::getEntityManager()->contains($stock));
    }

    public function testRemove(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);
        $this->assertInstanceOf(StockRepository::class, $repository);

        $stock = $this->createStock();
        $stock->setStockId('test-stock');

        self::getEntityManager()->persist($stock);
        self::getEntityManager()->flush();

        $repository->remove($stock, false);
        $this->assertFalse(self::getEntityManager()->contains($stock));
    }

    // find方法相关测试

    // count方法相关测试
    public function testCountWhenNoRecordsExistShouldReturnZero(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        // 使用不存在的条件查询，确保返回0
        $count = $repository->count(['stockId' => 'non-existent-stock-id']);

        $this->assertSame(0, $count);
    }

    public function testCountWhenRecordsExistShouldReturnCorrectCount(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setStockId('test-stock-1');
        $stock2 = $this->createStock();
        $stock2->setStockId('test-stock-2');

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        // DataFixtures 已加载数据，总数应该大于等于2
        $count = $repository->count([]);
        $this->assertGreaterThanOrEqual(2, $count);
    }

    public function testCountWithMatchingCriteriaShouldReturnCorrectCount(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setStatus(StockStatus::ONGOING);
        $stock2 = $this->createStock();
        $stock2->setStockId('stock-2');
        $stock2->setStatus(StockStatus::PAUSED);

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        $count = $repository->count(['status' => StockStatus::ONGOING]);

        // DataFixtures 可能包含 ONGOING 状态的数据，至少应该有我们添加的1个
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithNullFieldQueryShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setDescription(null);
        $stock2 = $this->createStock();
        $stock2->setStockId('stock-2');
        $stock2->setDescription('Test Description');

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        $count = $repository->count(['description' => null]);

        // DataFixtures 可能包含 description 为 null 的数据，至少应该有我们添加的1个
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithNotifyConfigNullShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setNotifyConfig(null);
        $stock2 = $this->createStock();
        $stock2->setStockId('stock-2');
        $stock2->setNotifyConfig(['key' => 'value']);

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        $count = $repository->count(['notifyConfig' => null]);

        // DataFixtures 可能包含 notifyConfig 为 null 的数据，至少应该有我们添加的1个
        $this->assertGreaterThanOrEqual(1, $count);
    }

    // findBy方法相关测试
    public function testFindByWhenNoRecordsExistShouldReturnEmptyArray(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        // 使用不存在的条件查询，确保返回空数组
        $result = $repository->findBy(['stockId' => 'non-existent-stock-id']);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindByWhenRecordsExistShouldReturnArrayOfEntities(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setStockId('test-find-stock-1');
        $stock2 = $this->createStock();
        $stock2->setStockId('test-find-stock-2');

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        $result = $repository->findBy([]);

        $this->assertIsArray($result);
        // DataFixtures 已加载数据，总数应该大于等于2
        $this->assertGreaterThanOrEqual(2, count($result));
        $this->assertContainsOnlyInstancesOf(Stock::class, $result);
    }

    public function testFindByWithNullFieldQueryShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setDescription(null);
        $stock2 = $this->createStock();
        $stock2->setStockId('stock-2');
        $stock2->setDescription('Test Description');

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        $result = $repository->findBy(['description' => null]);

        $this->assertIsArray($result);
        // DataFixtures 可能包含 description 为 null 的数据，至少应该有我们添加的1个
        $this->assertGreaterThanOrEqual(1, count($result));
        // 验证至少包含我们添加的实体
        $stockIds = array_map(fn ($stock) => $stock->getId(), $result);
        $this->assertContains($stock1->getId(), $stockIds);
    }

    public function testFindByWithNotifyConfigNullShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setNotifyConfig(null);
        $stock2 = $this->createStock();
        $stock2->setStockId('stock-2');
        $stock2->setNotifyConfig(['key' => 'value']);

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        $result = $repository->findBy(['notifyConfig' => null]);

        $this->assertIsArray($result);
        // DataFixtures 可能包含 notifyConfig 为 null 的数据，至少应该有我们添加的1个
        $this->assertGreaterThanOrEqual(1, count($result));
        // 验证至少包含我们添加的实体
        $stockIds = array_map(fn ($stock) => $stock->getId(), $result);
        $this->assertContains($stock1->getId(), $stockIds);
    }

    // findOneBy方法相关测试

    public function testFindOneByWithOrderByShouldReturnFirstOrderedResult(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setStockId('stock-1');
        $stock1->setStockName('B Stock');
        $stock2 = $this->createStock();
        $stock2->setStockId('stock-2');
        $stock2->setStockName('A Stock');

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        $result = $repository->findOneBy([], ['stockName' => 'ASC']);

        $this->assertNotNull($result);
        $this->assertSame('A Stock', $result->getStockName());
    }

    public function testFindOneByWithNullFieldQueryShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setStockId('unique-desc-null-stock-' . uniqid());
        $stock1->setDescription(null);
        $stock2 = $this->createStock();
        $stock2->setStockId('unique-desc-stock-' . uniqid());
        $stock2->setDescription('Test Description');

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        // 使用唯一的stockId作为查询条件，确保返回我们添加的数据
        $result = $repository->findOneBy([
            'stockId' => $stock1->getStockId(),
            'description' => null,
        ]);

        $this->assertNotNull($result);
        $this->assertSame($stock1->getId(), $result->getId());
    }

    public function testFindOneByWithNotifyConfigNullShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Stock::class);

        $stock1 = $this->createStock();
        $stock1->setStockId('unique-notify-null-stock-' . uniqid());
        $stock1->setNotifyConfig(null);
        $stock2 = $this->createStock();
        $stock2->setStockId('unique-notify-config-stock-' . uniqid());
        $stock2->setNotifyConfig(['key' => 'value']);

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        self::getEntityManager()->flush();

        // 使用唯一的stockId作为查询条件，确保返回我们添加的数据
        $result = $repository->findOneBy([
            'stockId' => $stock1->getStockId(),
            'notifyConfig' => null,
        ]);

        $this->assertNotNull($result);
        $this->assertSame($stock1->getId(), $result->getId());
        $this->assertNull($result->getNotifyConfig());
    }

    /**
     * 测试使用IS NULL查询可空字段时的排序逻辑
     */

    /**
     * 测试findOneBy查询description为null的实体
     */

    /**
     * 测试findOneBy查询notifyConfig为null的实体
     */

    /**
     * 测试findBy查询description为null的所有实体
     */

    /**
     * 测试count查询description为null的正确数量
     */

    // findAll方法相关测试

    /**
     * 创建测试用的Stock实体
     */
    private function createStock(): Stock
    {
        $stock = new Stock();
        $stock->setStockId('test-stock-' . uniqid());
        $stock->setStockName('Test Stock');
        $stock->setDescription('Test Description');
        $stock->setAvailableBeginTime([]);
        $stock->setAvailableEndTime([]);
        $stock->setStockUseRule([]);
        $stock->setCouponUseRule([]);
        $stock->setCustomEntrance([]);
        $stock->setDisplayPatternInfo([]);
        $stock->setNotifyConfig(null);
        $stock->setStatus(StockStatus::ONGOING);
        $stock->setMaxCoupons(100);
        $stock->setMaxCouponsPerUser(5);
        $stock->setMaxAmount(10000);
        $stock->setMaxAmountByDay(1000);
        $stock->setRemainAmount(10000);
        $stock->setDistributedCoupons(0);
        $stock->setNoLimit(false);

        return $stock;
    }

    protected function createNewEntity(): object
    {
        $entity = new Stock();

        // 设置基本字段
        $entity->setStockId('stock_' . uniqid());
        $entity->setStockName('Test Stock Name');
        $entity->setDescription('Test Stock');
        $entity->setStatus(StockStatus::ONGOING);
        $entity->setMaxCoupons(100);
        $entity->setMaxAmount(10000);
        $entity->setMaxCouponsPerUser(100);
        $entity->setMaxAmountByDay(1000);
        $entity->setRemainAmount(10000);
        $entity->setDistributedCoupons(0);
        $entity->setNoLimit(false);

        return $entity;
    }

    protected function getRepository(): StockRepository
    {
        return self::getService(StockRepository::class);
    }

    private function isTestingDatabaseConnection(): bool
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($backtrace as $trace) {
            if (str_contains($trace['function'], 'testFindWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testFindByWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testFindAllWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testCountWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testFindOneByWhenDatabaseIsUnavailable')) {
                return true;
            }
        }

        return false;
    }
}
