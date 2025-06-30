<?php

namespace WechatPayBusifavorBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use WechatPayBusifavorBundle\Command\SyncStockCommand;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Exception\BusifavorApiException;
use WechatPayBusifavorBundle\Repository\StockRepository;
use WechatPayBusifavorBundle\Service\BusifavorService;

class SyncStockCommandTest extends TestCase
{
    private BusifavorService $busifavorService;
    private StockRepository $stockRepository;
    private SyncStockCommand $command;

    protected function setUp(): void
    {
        $this->busifavorService = $this->createMock(BusifavorService::class);
        $this->stockRepository = $this->createMock(StockRepository::class);
        $this->command = new SyncStockCommand($this->busifavorService, $this->stockRepository);
    }

    public function testExecuteWithSpecificStockId(): void
    {
        $stockId = 'STOCK123';
        $mockResult = ['status' => 'ACTIVE'];

        $this->busifavorService->expects($this->once())
            ->method('getStock')
            ->with($stockId)
            ->willReturn($mockResult);

        $commandTester = new CommandTester($this->command);
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
        $stockId = 'STOCK123';
        $errorMessage = 'API Error';

        $this->busifavorService->expects($this->once())
            ->method('getStock')
            ->with($stockId)
            ->willThrowException(new BusifavorApiException($errorMessage));

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'stock-id' => $stockId,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('商家券批次 STOCK123 同步失败: ' . $errorMessage, $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testExecuteAllStocksSuccess(): void
    {
        $stock1 = $this->createMock(Stock::class);
        $stock1->expects($this->any())->method('getStockId')->willReturn('STOCK1');
        $stock1->expects($this->any())->method('getStockName')->willReturn('Stock 1');

        $stock2 = $this->createMock(Stock::class);
        $stock2->expects($this->any())->method('getStockId')->willReturn('STOCK2');
        $stock2->expects($this->any())->method('getStockName')->willReturn('Stock 2');

        $stocks = [$stock1, $stock2];

        $this->stockRepository->expects($this->once())
            ->method('findBy')
            ->with([], ['createdTime' => 'DESC'])
            ->willReturn($stocks);

        $this->busifavorService->expects($this->exactly(2))
            ->method('getStock')
            ->willReturnMap([
                ['STOCK1', ['status' => 'ACTIVE']],
                ['STOCK2', ['status' => 'EXPIRED']],
            ]);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('找到 2 个商家券批次', $output);
        $this->assertStringContainsString('所有 2 个商家券批次同步成功', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteAllStocksPartialFailure(): void
    {
        $stock1 = $this->createMock(Stock::class);
        $stock1->expects($this->any())->method('getStockId')->willReturn('STOCK1');
        $stock1->expects($this->any())->method('getStockName')->willReturn('Stock 1');

        $stock2 = $this->createMock(Stock::class);
        $stock2->expects($this->any())->method('getStockId')->willReturn('STOCK2');
        $stock2->expects($this->any())->method('getStockName')->willReturn('Stock 2');

        $stocks = [$stock1, $stock2];

        $this->stockRepository->expects($this->once())
            ->method('findBy')
            ->willReturn($stocks);

        $this->busifavorService->expects($this->exactly(2))
            ->method('getStock')
            ->willReturnCallback(function ($stockId) {
                if ($stockId === 'STOCK1') {
                    return ['status' => 'ACTIVE'];
                }
                throw new BusifavorApiException('API Error');
            });

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('同步完成. 成功: 1, 失败: 1', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteNoStocks(): void
    {
        $this->stockRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([]);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('找到 0 个商家券批次', $output);
        $this->assertStringContainsString('所有 0 个商家券批次同步成功', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testGetName(): void
    {
        $this->assertEquals(SyncStockCommand::NAME, $this->command->getName());
        $this->assertEquals('wechat-pay:busifavor:sync-stock', $this->command->getName());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals('同步微信商家券批次状态', $this->command->getDescription());
    }
}