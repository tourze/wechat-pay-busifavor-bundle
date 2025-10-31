<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatPayBusifavorBundle\Command\SyncStockCommand;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Exception\BusifavorApiException;
use WechatPayBusifavorBundle\Repository\StockRepository;
use WechatPayBusifavorBundle\Service\BusifavorService;

/**
 * @internal
 */
#[CoversClass(SyncStockCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncStockCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 使用Mock的Service以避免复杂的外部依赖注入
        // BusifavorService需要微信支付API客户端，在单元测试中用Mock替代
        $busifavorService = $this->createMock(BusifavorService::class);

        // StockRepository继承自Doctrine Repository基类，没有对应接口
        // 必须Mock具体类来模拟数据库查询操作，这是合理且必要的
        // 替代方案是创建Repository接口，但会增加代码复杂性且对简单CRUD操作过度设计
        $stockRepository = $this->createMock(StockRepository::class);

        $container = self::getContainer();
        $container->set(BusifavorService::class, $busifavorService);
        $container->set(StockRepository::class, $stockRepository);
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncStockCommand::class);
        $this->assertInstanceOf(SyncStockCommand::class, $command);

        return new CommandTester($command);
    }

    public function testExecuteWithSpecificStockId(): void
    {
        $busifavorService = self::getService(BusifavorService::class);
        $this->assertInstanceOf(MockObject::class, $busifavorService);
        $command = self::getService(SyncStockCommand::class);
        $this->assertInstanceOf(SyncStockCommand::class, $command);

        $stockId = 'STOCK123';
        $mockResult = ['status' => 'ACTIVE'];

        $busifavorService->expects($this->once())
            ->method('getStock')
            ->with($stockId)
            ->willReturn($mockResult)
        ;

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'stock-id' => $stockId,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('正在同步商家券批次：STOCK123', $output);
        $this->assertStringContainsString('商家券批次 STOCK123 同步成功，当前状态: ACTIVE', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithSpecificStockIdError(): void
    {
        $busifavorService = self::getService(BusifavorService::class);
        $this->assertInstanceOf(MockObject::class, $busifavorService);
        $command = self::getService(SyncStockCommand::class);
        $this->assertInstanceOf(SyncStockCommand::class, $command);

        $stockId = 'STOCK123';
        $errorMessage = 'API Error';

        $busifavorService->expects($this->once())
            ->method('getStock')
            ->with($stockId)
            ->willThrowException(new BusifavorApiException($errorMessage))
        ;

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'stock-id' => $stockId,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('商家券批次 STOCK123 同步失败: ' . $errorMessage, $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testExecuteAllStocksSuccess(): void
    {
        $stockRepository = self::getService(StockRepository::class);
        $this->assertInstanceOf(MockObject::class, $stockRepository);
        $busifavorService = self::getService(BusifavorService::class);
        $this->assertInstanceOf(MockObject::class, $busifavorService);
        $command = self::getService(SyncStockCommand::class);
        $this->assertInstanceOf(SyncStockCommand::class, $command);

        // Stock是Doctrine实体类，没有对应接口
        // 必须Mock具体类来模拟数据库中的批次数据，控制测试数据返回
        // 替代方案是使用真实实体，但会增加测试复杂性和数据库依赖
        $stock1 = $this->createMock(Stock::class);
        $stock1->expects($this->any())->method('getStockId')->willReturn('STOCK1');
        $stock1->expects($this->any())->method('getStockName')->willReturn('Stock 1');

        // Stock是Doctrine实体类，需要Mock第二个实例进行成功测试对比
        // 必须使用具体类来模拟不同状态的Stock对象，这是合理的测试实践
        // 替代方案是创建多个真实实体，但会增加测试数据管理复杂性
        $stock2 = $this->createMock(Stock::class);
        $stock2->expects($this->any())->method('getStockId')->willReturn('STOCK2');
        $stock2->expects($this->any())->method('getStockName')->willReturn('Stock 2');

        $stocks = [$stock1, $stock2];

        $stockRepository->expects($this->once())
            ->method('findBy')
            ->with([], ['createdTime' => 'DESC'])
            ->willReturn($stocks)
        ;

        $busifavorService->expects($this->exactly(2))
            ->method('getStock')
            ->willReturnMap([
                ['STOCK1', ['status' => 'ACTIVE']],
                ['STOCK2', ['status' => 'EXPIRED']],
            ])
        ;

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('找到 2 个商家券批次', $output);
        $this->assertStringContainsString('所有 2 个商家券批次同步成功', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteAllStocksPartialFailure(): void
    {
        $stockRepository = self::getService(StockRepository::class);
        $this->assertInstanceOf(MockObject::class, $stockRepository);
        $busifavorService = self::getService(BusifavorService::class);
        $this->assertInstanceOf(MockObject::class, $busifavorService);
        $command = self::getService(SyncStockCommand::class);
        $this->assertInstanceOf(SyncStockCommand::class, $command);

        // Stock是Doctrine实体类，测试部分失败场景需要Mock具体类
        // 必须使用具体类Mock来模拟异常处理逻辑，这是合理且必要的
        // 替代方案是使用真实实体，但会增加测试的复杂性和不稳定性
        $stock1 = $this->createMock(Stock::class);
        $stock1->expects($this->any())->method('getStockId')->willReturn('STOCK1');
        $stock1->expects($this->any())->method('getStockName')->willReturn('Stock 1');

        // Stock是Doctrine实体类，需要Mock第二个实例进行失败测试对比
        // 必须使用具体类来模拟异常处理场景，这是合理且必要的
        // 替代方案是创建真实实体，但会增加测试的复杂性和不稳定性
        $stock2 = $this->createMock(Stock::class);
        $stock2->expects($this->any())->method('getStockId')->willReturn('STOCK2');
        $stock2->expects($this->any())->method('getStockName')->willReturn('Stock 2');

        $stocks = [$stock1, $stock2];

        $stockRepository->expects($this->once())
            ->method('findBy')
            ->willReturn($stocks)
        ;

        $busifavorService->expects($this->exactly(2))
            ->method('getStock')
            ->willReturnCallback(function ($stockId) {
                if ('STOCK1' === $stockId) {
                    return ['status' => 'ACTIVE'];
                }
                throw new BusifavorApiException('API Error');
            })
        ;

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('同步完成. 成功: 1, 失败: 1', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteNoStocks(): void
    {
        $stockRepository = self::getService(StockRepository::class);
        $this->assertInstanceOf(MockObject::class, $stockRepository);
        $command = self::getService(SyncStockCommand::class);
        $this->assertInstanceOf(SyncStockCommand::class, $command);

        $stockRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([])
        ;

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('找到 0 个商家券批次', $output);
        $this->assertStringContainsString('所有 0 个商家券批次同步成功', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testArgumentStockId(): void
    {
        $commandTester = $this->getCommandTester();

        // Test with stock-id argument
        $commandTester->execute([
            'stock-id' => 'test_stock_id',
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testGetName(): void
    {
        $command = self::getService(SyncStockCommand::class);
        $this->assertInstanceOf(SyncStockCommand::class, $command);

        $this->assertEquals(SyncStockCommand::NAME, $command->getName());
        $this->assertEquals('wechat-pay:busifavor:sync-stock', $command->getName());
    }

    public function testGetDescription(): void
    {
        $command = self::getService(SyncStockCommand::class);
        $this->assertInstanceOf(SyncStockCommand::class, $command);

        $this->assertEquals('同步微信商家券批次状态', $command->getDescription());
    }
}
