<?php

namespace WechatPayBusifavorBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatPayBusifavorBundle\Client\BusifavorClient;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\CouponStatus;
use WechatPayBusifavorBundle\Enum\StockStatus;
use WechatPayBusifavorBundle\Repository\CouponRepository;
use WechatPayBusifavorBundle\Repository\StockRepository;
use WechatPayBusifavorBundle\Service\BusifavorService;

class BusifavorServiceTest extends TestCase
{
    private BusifavorClient|MockObject $client;
    private StockRepository|MockObject $stockRepository;
    private CouponRepository|MockObject $couponRepository;
    private LoggerInterface|MockObject $logger;
    private EntityManagerInterface|MockObject $entityManager;
    private BusifavorService $service;

    protected function setUp(): void
    {
        $this->client = $this->createMock(BusifavorClient::class);
        $this->stockRepository = $this->createMock(StockRepository::class);
        $this->couponRepository = $this->createMock(CouponRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->service = new BusifavorService(
            $this->client,
            $this->stockRepository,
            $this->couponRepository,
            $this->logger,
            $this->entityManager
        );
    }

    public function testCreateStock_withValidData(): void
    {
        // 测试数据
        $stockData = [
            'stock_name' => '测试商家券批次',
            'comment' => '测试描述',
            'available_begin_time' => ['value' => '2023-01-01T00:00:00+08:00'],
            'available_end_time' => ['value' => '2023-12-31T23:59:59+08:00'],
            'stock_use_rule' => [
                'max_coupons' => 100,
                'max_coupons_per_user' => 10,
                'max_amount' => 10000,
                'max_amount_by_day' => 1000,
            ],
            'coupon_use_rule' => [
                'fixed_normal_coupon' => [
                    'discount_amount' => 100,
                    'transaction_minimum' => 100,
                ],
            ],
            'custom_entrance' => [],
            'display_pattern_info' => [],
        ];

        // 模拟微信API响应
        $apiResponse = [
            'stock_id' => 'test_stock_id_123456',
            'create_time' => '2023-01-01T00:00:00+08:00',
            'status' => 'UNAUDIT',
        ];

        // 设置Mock行为
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);

        // 设置期望的持久化行为
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($stock) use ($apiResponse, $stockData) {
                return $stock instanceof Stock 
                    && $stock->getStockId() === $apiResponse['stock_id']
                    && $stock->getStockName() === $stockData['stock_name']
                    && $stock->getMaxCoupons() === $stockData['stock_use_rule']['max_coupons'];
            }));

        $this->entityManager->expects($this->once())
            ->method('flush');

        // 执行测试
        $result = $this->service->createStock($stockData);

        // 验证结果
        $this->assertEquals($apiResponse, $result);
    }

    public function testCreateStock_withApiError(): void
    {
        // 测试数据
        $stockData = [
            'stock_name' => '测试商家券批次',
            'stock_use_rule' => [
                'max_coupons' => 100,
            ],
        ];

        // 设置Mock行为 - 模拟API抛出异常
        $exception = new \Exception('API调用失败');
        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        // 设置日志记录预期
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('创建商家券批次失败'),
                $this->callback(function ($context) use ($stockData, $exception) {
                    return isset($context['data']) 
                        && $context['data'] === $stockData
                        && $context['exception'] instanceof \Exception;
                })
            );

        // 设置期望 - 不应调用持久化
        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->entityManager->expects($this->never())
            ->method('flush');

        // 执行测试并验证异常
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API调用失败');
        
        $this->service->createStock($stockData);
    }

    public function testGetStock_withValidStockId(): void
    {
        // 测试数据
        $stockId = 'test_stock_id_123456';
        
        // 模拟微信API响应
        $apiResponse = [
            'stock_id' => $stockId,
            'stock_name' => '新名称',
            'stock_state' => StockStatus::ONGOING->value, // 使用枚举值
            'available_begin_time' => '2023-01-01T00:00:00+08:00',
            'available_end_time' => '2023-12-31T23:59:59+08:00',
            'stock_use_rule' => [
                'max_coupons' => 100,
            ],
            'coupon_use_rule' => [
                'fixed_normal_coupon' => [
                    'discount_amount' => 100,
                ],
            ],
            'custom_entrance' => [],
            'display_pattern_info' => [],
        ];
        
        // 创建模拟服务
        $mockService = $this->getMockBuilder(BusifavorService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStock'])
            ->getMock();
            
        $mockService->expects($this->once())
            ->method('getStock')
            ->with($stockId)
            ->willReturn($apiResponse);
        
        // 执行测试
        $result = $mockService->getStock($stockId);
        
        // 验证结果
        $this->assertEquals($apiResponse, $result);
    }

    public function testUseCoupon_withValidData(): void
    {
        // 测试数据
        $couponCode = 'test_coupon_code';
        $stockId = 'test_stock_id';
        $appid = 'test_appid';
        $openid = 'test_openid';
        $useRequestNo = 'test_request_no';
        
        // 测试请求数据
        $requestData = [
            'coupon_code' => $couponCode,
            'stock_id' => $stockId,
            'appid' => $appid,
            'openid' => $openid,
            'use_request_no' => $useRequestNo,
        ];
        
        // 模拟数据库中已存在的Coupon
        $coupon = new Coupon();
        $coupon->setCouponCode($couponCode);
        $coupon->setStockId($stockId);
        $coupon->setOpenid($openid);
        $coupon->setStatus(CouponStatus::SENDED);
        
        // 模拟微信API响应
        $apiResponse = [
            'wechatpay_use_time' => '2023-01-01T12:00:00+08:00',
            'use_request_no' => $useRequestNo,
            'use_time' => '2023-01-01T12:00:00+08:00',
        ];
        
        // 设置Mock行为
        $this->couponRepository->expects($this->once())
            ->method('findByCouponCode')
            ->with($couponCode)
            ->willReturn($coupon);
            
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
        
        // 设置期望的持久化行为
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($updatedCoupon) {
                return $updatedCoupon instanceof Coupon 
                    && $updatedCoupon->getStatus() === CouponStatus::USED;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->service->useCoupon($couponCode, $stockId, $appid, $openid, $useRequestNo);
        
        // 验证结果
        $this->assertEquals($apiResponse, $result);
    }

    public function testGetCoupon_withNewCoupon(): void
    {
        // 测试数据
        $couponCode = 'test_coupon_code';
        $openid = 'test_openid';
        $appid = 'test_appid';
        $stockId = 'test_stock_id';
        
        // 模拟微信API响应（新券）
        $apiResponse = [
            'coupon_code' => $couponCode,
            'stock_id' => $stockId,
            'status' => CouponStatus::SENDED->value,
            'belong_merchant' => 'test_merchant',
            'coupon_type' => 'NORMAL',
            'expire_time' => '2023-12-31T23:59:59+08:00',
        ];
        
        // 设置Mock行为 - 数据库中不存在该券
        $this->couponRepository->expects($this->once())
            ->method('findByCouponCode')
            ->with($couponCode)
            ->willReturn(null);
            
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
        
        // 设置期望的持久化行为 - 应创建新券
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($newCoupon) use ($couponCode, $stockId, $openid) {
                return $newCoupon instanceof Coupon 
                    && $newCoupon->getCouponCode() === $couponCode
                    && $newCoupon->getStockId() === $stockId
                    && $newCoupon->getOpenid() === $openid
                    && $newCoupon->getStatus() === CouponStatus::SENDED;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->service->getCoupon($couponCode, $openid, $appid);
        
        // 验证结果
        $this->assertEquals($apiResponse, $result);
    }

    public function testGetUserCoupons_withValidParameters(): void
    {
        // 测试数据
        $openid = 'test_openid';
        $appid = 'test_appid';
        $stockId = 'test_stock_id';
        $status = 'SENDED';
        $offset = 0;
        $limit = 10;
        
        // 模拟微信API响应
        $apiResponse = [
            'data' => [
                [
                    'coupon_code' => 'coupon1',
                    'stock_id' => $stockId,
                    'status' => 'SENDED',
                ],
                [
                    'coupon_code' => 'coupon2',
                    'stock_id' => $stockId,
                    'status' => 'USED',
                ],
            ],
            'total_count' => 2,
            'limit' => 10,
            'offset' => 0,
        ];
        
        // 使用模拟服务
        $mockService = $this->getMockBuilder(BusifavorService::class)
            ->setConstructorArgs([
                $this->client,
                $this->stockRepository,
                $this->couponRepository,
                $this->logger,
                $this->entityManager
            ])
            ->onlyMethods(['getUserCoupons'])
            ->getMock();
            
        $mockService->expects($this->once())
            ->method('getUserCoupons')
            ->with($openid, $appid, $stockId, $status, $offset, $limit)
            ->willReturn($apiResponse);
        
        // 执行测试
        $result = $mockService->getUserCoupons($openid, $appid, $stockId, $status, $offset, $limit);
        
        // 验证结果
        $this->assertEquals($apiResponse, $result);
    }
} 