<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatPayBusifavorBundle\Command\ListUserCouponsCommand;
use WechatPayBusifavorBundle\Exception\BusifavorApiException;
use WechatPayBusifavorBundle\Service\BusifavorService;

/**
 * @internal
 */
#[CoversClass(ListUserCouponsCommand::class)]
#[RunTestsInSeparateProcesses]
final class ListUserCouponsCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // BusifavorService是业务服务类，需要复杂的外部依赖（微信支付API客户端等）
        // 必须Mock具体类因为：1) 没有对应接口 2) 避免复杂的外部API依赖 3) 专注测试Command逻辑
        // 替代方案是创建接口，但会增加代码复杂性且这是内部服务不需要接口抽象
        $busifavorService = $this->createMock(BusifavorService::class);

        $container = self::getContainer();
        $container->set(BusifavorService::class, $busifavorService);
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(ListUserCouponsCommand::class);
        $this->assertInstanceOf(ListUserCouponsCommand::class, $command);

        return new CommandTester($command);
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

        /** @var BusifavorService&MockObject $busifavorService */
        $busifavorService = self::getService(BusifavorService::class);
        $busifavorService->expects($this->once())
            ->method('getUserCoupons')
            ->with($openid, 'test_appid', null, null, 0, 10)
            ->willReturn($mockResult)
        ;

        $command = self::getService(ListUserCouponsCommand::class);
        $this->assertInstanceOf(ListUserCouponsCommand::class, $command);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'openid' => $openid,
            '--appid' => 'test_appid',
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

        /** @var BusifavorService&MockObject $busifavorService */
        $busifavorService = self::getService(BusifavorService::class);
        $busifavorService->expects($this->once())
            ->method('getUserCoupons')
            ->with($openid, 'test_appid', null, null, 0, 10)
            ->willReturn($mockResult)
        ;

        $command = self::getService(ListUserCouponsCommand::class);
        $this->assertInstanceOf(ListUserCouponsCommand::class, $command);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'openid' => $openid,
            '--appid' => 'test_appid',
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

        /** @var BusifavorService&MockObject $busifavorService */
        $busifavorService = self::getService(BusifavorService::class);
        $busifavorService->expects($this->once())
            ->method('getUserCoupons')
            ->with($openid, $customAppid, $stockId, $status, $offset, $limit)
            ->willReturn(['data' => []])
        ;

        $command = self::getService(ListUserCouponsCommand::class);
        $this->assertInstanceOf(ListUserCouponsCommand::class, $command);
        $commandTester = new CommandTester($command);
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

        /** @var BusifavorService&MockObject $busifavorService */
        $busifavorService = self::getService(BusifavorService::class);
        $busifavorService->expects($this->once())
            ->method('getUserCoupons')
            ->with($openid, 'test_appid', null, null, 0, 10)
            ->willThrowException(new BusifavorApiException($errorMessage))
        ;

        $command = self::getService(ListUserCouponsCommand::class);
        $this->assertInstanceOf(ListUserCouponsCommand::class, $command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'openid' => $openid,
            '--appid' => 'test_appid',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('查询失败: ' . $errorMessage, $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testArgumentOpenid(): void
    {
        $commandTester = $this->getCommandTester();

        // Test missing required argument
        $this->expectException(RuntimeException::class);
        $commandTester->execute([]);
    }

    public function testOptionAppid(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'openid' => 'test_openid',
            '--appid' => 'test_appid',
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testOptionStockId(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'openid' => 'test_openid',
            '--appid' => 'test_appid',
            '--stock-id' => 'test_stock_id',
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testOptionStatus(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'openid' => 'test_openid',
            '--appid' => 'test_appid',
            '--status' => 'USED',
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testOptionLimit(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'openid' => 'test_openid',
            '--appid' => 'test_appid',
            '--limit' => '20',
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testOptionOffset(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'openid' => 'test_openid',
            '--appid' => 'test_appid',
            '--offset' => '10',
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testGetName(): void
    {
        $command = self::getService(ListUserCouponsCommand::class);
        $this->assertInstanceOf(ListUserCouponsCommand::class, $command);
        $this->assertEquals(ListUserCouponsCommand::NAME, $command->getName());
        $this->assertEquals('wechat-pay:busifavor:list-user-coupons', $command->getName());
    }

    public function testGetDescription(): void
    {
        $command = self::getService(ListUserCouponsCommand::class);
        $this->assertInstanceOf(ListUserCouponsCommand::class, $command);
        $this->assertEquals('查询用户的商家券列表', $command->getDescription());
    }
}
