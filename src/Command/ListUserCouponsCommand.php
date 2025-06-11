<?php

namespace WechatPayBusifavorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WechatPayBusifavorBundle\Service\BusifavorService;

#[AsCommand(
    name: ListUserCouponsCommand::NAME,
    description: '查询用户的商家券列表',
)]
class ListUserCouponsCommand extends Command
{
    public const NAME = 'wechat-pay:busifavor:list-user-coupons';

    public function __construct(
        private readonly BusifavorService $busifavorService,
        private readonly ParameterBagInterface $parameterBag,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('openid', InputArgument::REQUIRED, '用户的openid')
            ->addOption('appid', null, InputOption::VALUE_OPTIONAL, '公众号/小程序的appid')
            ->addOption('stock-id', null, InputOption::VALUE_OPTIONAL, '商家券批次ID')
            ->addOption('status', null, InputOption::VALUE_OPTIONAL, '券状态: SENDED/USED/EXPIRED')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, '查询数量限制', 10)
            ->addOption('offset', null, InputOption::VALUE_OPTIONAL, '查询偏移量', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $openid = $input->getArgument('openid');
        $appid = $input->getOption('appid') ?? $this->parameterBag->get('wechat_appid');
        $stockId = $input->getOption('stock-id');
        $status = $input->getOption('status');
        $limit = (int) $input->getOption('limit');
        $offset = (int) $input->getOption('offset');

        $io->note(sprintf('正在查询 %s 的商家券列表...', $openid));

        try {
            $result = $this->busifavorService->getUserCoupons($openid, $appid, $stockId, $status, $offset, $limit);

            if (empty($result['data'])) {
                $io->warning('未找到符合条件的商家券');

                return Command::SUCCESS;
            }

            $rows = [];
            foreach ($result['data'] as $coupon) {
                $rows[] = [
                    $coupon['coupon_code'] ?? 'N/A',
                    $coupon['stock_id'] ?? 'N/A',
                    $coupon['status'] ?? 'N/A',
                    $coupon['create_time'] ?? 'N/A',
                    $coupon['use_time'] ?? 'N/A',
                    $coupon['expire_time'] ?? 'N/A',
                ];
            }

            $io->table(
                ['券码', '批次ID', '状态', '创建时间', '使用时间', '过期时间'],
                $rows
            );

            $io->success(sprintf('共显示 %d 条数据', count($result['data'])));
        } catch (\Throwable $e) {
            $io->error(sprintf('查询失败: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
