<?php

namespace WechatPayBusifavorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WechatPayBusifavorBundle\Repository\StockRepository;
use WechatPayBusifavorBundle\Service\BusifavorService;

#[AsCommand(
    name: SyncStockCommand::NAME,
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
            ->addArgument('stock-id', InputArgument::OPTIONAL, '商家券批次ID，不提供则同步所有批次');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $stockId = $input->getArgument('stock-id');

        if ($stockId) {
            // 同步指定批次
            $io->note(sprintf('正在同步商家券批次：%s', $stockId));

            try {
                $result = $this->busifavorService->getStock($stockId);
                $io->success(sprintf('商家券批次 %s 同步成功，当前状态: %s', $stockId, $result['status'] ?? 'unknown'));
            } catch  (\Throwable $e) {
                $io->error(sprintf('商家券批次 %s 同步失败: %s', $stockId, $e->getMessage()));

                return Command::FAILURE;
            }
        } else {
            // 同步所有批次
            $stocks = $this->stockRepository->findBy([], ['createdTime' => 'DESC']);
            $io->note(sprintf('找到 %d 个商家券批次，开始同步状态...', count($stocks)));

            $successCount = 0;
            $failedCount = 0;

            foreach ($stocks as $stock) {
                $io->section(sprintf('同步批次: %s (%s)', $stock->getStockId(), $stock->getStockName()));

                try {
                    $result = $this->busifavorService->getStock($stock->getStockId());
                    $io->writeln(sprintf('状态: %s', $result['status'] ?? 'unknown'));
                    ++$successCount;
                } catch  (\Throwable $e) {
                    $io->writeln(sprintf('<error>同步失败: %s</error>', $e->getMessage()));
                    ++$failedCount;
                }
            }

            if (0 === $failedCount) {
                $io->success(sprintf('所有 %d 个商家券批次同步成功', $successCount));
            } else {
                $io->warning(sprintf('同步完成. 成功: %d, 失败: %d', $successCount, $failedCount));
            }
        }

        return Command::SUCCESS;
    }
}
