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
    name: self::NAME,
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
            ->addOption('offset', null, InputOption::VALUE_OPTIONAL, '查询偏移量', 0)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $queryParams = $this->extractQueryParameters($input);
        $io->note(sprintf('正在查询 %s 的商家券列表...', $queryParams['openid']));

        try {
            $result = $this->busifavorService->getUserCoupons(
                $queryParams['openid'],
                $queryParams['appid'],
                $queryParams['stockId'],
                $queryParams['status'],
                $queryParams['offset'],
                $queryParams['limit']
            );

            return $this->displayResults($io, $result);
        } catch (\Throwable $e) {
            $io->error(sprintf('查询失败: %s', $e->getMessage()));

            return Command::FAILURE;
        }
    }

    /**
     * @return array{openid: string, appid: string, stockId: string|null, status: string|null, offset: int, limit: int}
     */
    private function extractQueryParameters(InputInterface $input): array
    {
        $openidRaw = $input->getArgument('openid');
        $openid = is_string($openidRaw) ? $openidRaw : '';

        $appidRaw = $input->getOption('appid') ?? $this->parameterBag->get('wechat_appid');
        $appid = is_string($appidRaw) ? $appidRaw : '';

        $stockId = $input->getOption('stock-id');
        $status = $input->getOption('status');

        $limitRaw = $input->getOption('limit');
        $limit = is_numeric($limitRaw) ? (int) $limitRaw : 10;

        $offsetRaw = $input->getOption('offset');
        $offset = is_numeric($offsetRaw) ? (int) $offsetRaw : 0;

        return [
            'openid' => $openid,
            'appid' => $appid,
            'stockId' => is_string($stockId) ? $stockId : null,
            'status' => is_string($status) ? $status : null,
            'offset' => $offset,
            'limit' => $limit,
        ];
    }

    /**
     * @param array<string, mixed> $result
     */
    private function displayResults(SymfonyStyle $io, array $result): int
    {
        if (!isset($result['data']) || [] === $result['data']) {
            $io->warning('未找到符合条件的商家券');

            return Command::SUCCESS;
        }

        $data = $result['data'];
        if (!is_array($data)) {
            $io->warning('返回数据格式异常');

            return Command::SUCCESS;
        }

        $rows = $this->formatTableRows($data);
        $io->table(
            ['券码', '批次ID', '状态', '创建时间', '使用时间', '过期时间'],
            $rows
        );

        $io->success(sprintf('共显示 %d 条数据', count($data)));

        return Command::SUCCESS;
    }

    /**
     * @param array<mixed> $data
     * @return array<array<string>>
     */
    private function formatTableRows(array $data): array
    {
        $rows = [];
        foreach ($data as $coupon) {
            if (is_array($coupon)) {
                /** @var array<string, mixed> $coupon */
                $rows[] = $this->formatCouponRow($coupon);
            }
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $coupon
     * @return array<string>
     */
    private function formatCouponRow(array $coupon): array
    {
        return [
            $this->getStringValue($coupon, 'coupon_code'),
            $this->getStringValue($coupon, 'stock_id'),
            $this->getStringValue($coupon, 'status'),
            $this->getStringValue($coupon, 'create_time'),
            $this->getStringValue($coupon, 'use_time'),
            $this->getStringValue($coupon, 'expire_time'),
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getStringValue(array $data, string $key): string
    {
        return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : 'N/A';
    }
}
