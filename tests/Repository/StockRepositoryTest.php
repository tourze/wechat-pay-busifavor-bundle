<?php

namespace WechatPayBusifavorBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\StockStatus;
use WechatPayBusifavorBundle\Repository\StockRepository;

class StockRepositoryTest extends TestCase
{
    /**
     * 测试 findByStockId 方法
     */
    public function testFindByStockId(): void
    {
        // 创建模拟对象
        $mockRepository = $this->getMockBuilder(StockRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();
            
        // 准备测试数据
        $stockId = 'test_stock_id';
        $stock = new Stock();
        $stock->setStockId($stockId);
        
        // 设置预期行为
        $mockRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['stockId' => $stockId])
            ->willReturn($stock);
            
        // 执行测试
        $result = $mockRepository->findByStockId($stockId);
        
        // 验证结果
        $this->assertSame($stock, $result);
    }
    
    /**
     * 测试 findActiveStocks 方法
     */
    public function testFindActiveStocks(): void
    {
        // 创建所需的模拟对象
        $registry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        
        // 创建特定的 Query 对象，而不是 AbstractQuery
        $query = $this->getMockBuilder(\Doctrine\ORM\Query::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResult'])
            ->getMock();
        
        // 使用部分模拟
        $repository = $this->getMockBuilder(StockRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
            
        // 准备测试数据
        $expectedStocks = [
            (new Stock())->setStockId('stock1')->setStatus(StockStatus::ONGOING),
            (new Stock())->setStockId('stock2')->setStatus(StockStatus::ONGOING),
        ];
        
        // 设置模拟行为链
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('s')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('s.status = :status')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('status', StockStatus::ONGOING)
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('s.createdTime', 'DESC')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $query->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedStocks);
            
        // 执行测试
        $result = $repository->findActiveStocks();
        
        // 验证结果
        $this->assertSame($expectedStocks, $result);
    }
    
    /**
     * 测试 findStocksByStatus 方法
     */
    public function testFindStocksByStatus(): void
    {
        // 创建所需的模拟对象
        $registry = $this->createMock(ManagerRegistry::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        
        // 创建特定的 Query 对象，而不是 AbstractQuery
        $query = $this->getMockBuilder(\Doctrine\ORM\Query::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResult'])
            ->getMock();
        
        // 使用部分模拟
        $repository = $this->getMockBuilder(StockRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
            
        // 准备测试数据
        $status = StockStatus::EXPIRED->value;
        $expectedStocks = [
            (new Stock())->setStockId('stock1')->setStatus(StockStatus::EXPIRED),
            (new Stock())->setStockId('stock2')->setStatus(StockStatus::EXPIRED),
        ];
        
        // 设置模拟行为链
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('s')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('s.status = :status')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('status', $status)
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('s.createdTime', 'DESC')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $query->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedStocks);
            
        // 执行测试
        $result = $repository->findStocksByStatus($status);
        
        // 验证结果
        $this->assertSame($expectedStocks, $result);
    }
    
    /**
     * 测试 findStocksByStatusEnum 方法
     */
    public function testFindStocksByStatusEnum(): void
    {
        // 创建模拟对象
        $mockRepository = $this->getMockBuilder(StockRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findStocksByStatus'])
            ->getMock();
            
        // 准备测试数据
        $status = StockStatus::PAUSED;
        $expectedStocks = [
            new Stock(),
            new Stock(),
        ];
        
        // 设置预期行为
        $mockRepository->expects($this->once())
            ->method('findStocksByStatus')
            ->with($status->value)
            ->willReturn($expectedStocks);
            
        // 执行测试
        $result = $mockRepository->findStocksByStatusEnum($status);
        
        // 验证结果
        $this->assertSame($expectedStocks, $result);
    }
} 