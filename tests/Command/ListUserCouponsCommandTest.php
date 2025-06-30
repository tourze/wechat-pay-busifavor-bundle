<?php

namespace WechatPayBusifavorBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use WechatPayBusifavorBundle\Command\ListUserCouponsCommand;
use WechatPayBusifavorBundle\Exception\BusifavorApiException;
use WechatPayBusifavorBundle\Service\BusifavorService;

class ListUserCouponsCommandTest extends TestCase
{
    private BusifavorService $busifavorService;
    private ParameterBag $parameterBag;
    private ListUserCouponsCommand $command;

    protected function setUp(): void
    {
        $this->busifavorService = $this->createMock(BusifavorService::class);
        $this->parameterBag = new ParameterBag(['wechat_appid' => 'test_appid']);
        $this->command = new ListUserCouponsCommand($this->busifavorService, $this->parameterBag);
    }

    public function testExecuteSuccessWithCoupons(): void
    {
        $openid = 'test_openid';
        $mockResult = [
            'data' => [
                [
                    'coupon_code' => 'COUPON123',
                    'stock_id' => 'STOCK123',
                    'status' => 'SENDED',
                    'create_time' => '2023-01-01 10:00:00',
                    'use_time' => null,
                    'expire_time' => '2023-12-31 23:59:59',
                ],
            ],
        ];

        $this->busifavorService->expects($this->once())
            ->method('getUserCoupons')
            ->with($openid, 'test_appid', null, null, 0, 10)
            ->willReturn($mockResult);

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'openid' => $openid,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('COUPON123', $output);
        $this->assertStringContainsString('STOCK123', $output);
        $this->assertStringContainsString('共显示 1 条数据', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteSuccessNoCoupons(): void
    {
        $openid = 'test_openid';
        $mockResult = ['data' => []];

        $this->busifavorService->expects($this->once())
            ->method('getUserCoupons')
            ->willReturn($mockResult);

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'openid' => $openid,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('未找到符合条件的商家券', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithOptions(): void
    {
        $openid = 'test_openid';
        $customAppid = 'custom_appid';
        $stockId = 'STOCK456';
        $status = 'USED';
        $limit = 20;
        $offset = 5;

        $this->busifavorService->expects($this->once())
            ->method('getUserCoupons')
            ->with($openid, $customAppid, $stockId, $status, $offset, $limit)
            ->willReturn(['data' => []]);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'openid' => $openid,
            '--appid' => $customAppid,
            '--stock-id' => $stockId,
            '--status' => $status,
            '--limit' => $limit,
            '--offset' => $offset,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithError(): void
    {
        $openid = 'test_openid';
        $errorMessage = 'API Error';

        $this->busifavorService->expects($this->once())
            ->method('getUserCoupons')
            ->willThrowException(new BusifavorApiException($errorMessage));

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'openid' => $openid,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('查询失败: ' . $errorMessage, $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testGetName(): void
    {
        $this->assertEquals(ListUserCouponsCommand::NAME, $this->command->getName());
        $this->assertEquals('wechat-pay:busifavor:list-user-coupons', $this->command->getName());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals('查询用户的商家券列表', $this->command->getDescription());
    }
}