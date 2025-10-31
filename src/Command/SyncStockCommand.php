<?php

namespace WechatPayBusifavorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Repository\StockRepository;
use WechatPayBusifavorBundle\Service\BusifavorService;

#[AsCommand(
    name: self::NAME,
    description: '同步微信商家券批次状态',
)]
class SyncStockCommand extends Command
{
    public const NAME = 'wechat-pay:busifavor:sync-stock';

    public function __construct(
        private readonly BusifavorService $busifavorService,
        private readonly StockRepository $stockRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('stock-id', InputArgument::OPTIONAL, '商家券批次ID，不提供则同步所有批次')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $stockId = $input->getArgument('stock-id');

        if (null !== $stockId && is_string($stockId)) {
            return $this->syncSingleStock($io, $stockId);
        }

        return $this->syncAllStocks($io);
    }

    private function syncSingleStock(SymfonyStyle $io, string $stockId): int
    {
        $io->note(sprintf('正在同步商家券批次：%s', $stockId));

        try {
            $result = $this->busifavorService->getStock($stockId);
            $status = isset($result['status']) && is_string($result['status']) ? $result['status'] : 'unknown';
            $io->success(sprintf('商家券批次 %s 同步成功，当前状态: %s', $stockId, $status));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error(sprintf('商家券批次 %s 同步失败: %s', $stockId, $e->getMessage()));

            return Command::FAILURE;
        }
    }

    private function syncAllStocks(SymfonyStyle $io): int
    {
        /** @var Stock[] $stocks */
        $stocks = $this->stockRepository->findBy([], ['createdTime' => 'DESC']);
        $io->note(sprintf('找到 %d 个商家券批次，开始同步状态...', count($stocks)));

        $successCount = 0;
        $failedCount = 0;

        foreach ($stocks as $stock) {
            $syncResult = $this->syncStockItem($io, $stock);
            if ($syncResult) {
                ++$successCount;
            } else {
                ++$failedCount;
            }
        }

        $this->displaySyncResults($io, $successCount, $failedCount);

        return Command::SUCCESS;
    }

    private function syncStockItem(SymfonyStyle $io, Stock $stock): bool
    {
        $io->section(sprintf('同步批次: %s (%s)', $stock->getStockId(), $stock->getStockName()));

        try {
            $result = $this->busifavorService->getStock($stock->getStockId());
            $status = isset($result['status']) && is_string($result['status']) ? $result['status'] : 'unknown';
            $io->writeln(sprintf('状态: %s', $status));

            return true;
        } catch (\Throwable $e) {
            $io->writeln(sprintf('<error>同步失败: %s</error>', $e->getMessage()));

            return false;
        }
    }

    private function displaySyncResults(SymfonyStyle $io, int $successCount, int $failedCount): void
    {
        if (0 === $failedCount) {
            $io->success(sprintf('所有 %d 个商家券批次同步成功', $successCount));
        } else {
            $io->warning(sprintf('同步完成. 成功: %d, 失败: %d', $successCount, $failedCount));
        }
    }
}
