<?php

namespace WechatPayBusifavorBundle\Service;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;
use WechatPayBusifavorBundle\Client\BusifavorClient;
use WechatPayBusifavorBundle\DTO\StockData;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\CouponStatus;
use WechatPayBusifavorBundle\Repository\CouponRepository;
use WechatPayBusifavorBundle\Repository\StockRepository;
use WechatPayBusifavorBundle\Request\CreateStockRequest;
use WechatPayBusifavorBundle\Request\GetCouponRequest;
use WechatPayBusifavorBundle\Request\GetStockRequest;
use WechatPayBusifavorBundle\Request\GetUserCouponsRequest;
use WechatPayBusifavorBundle\Request\UseCouponRequest;
use WechatPayBusifavorBundle\Service\CouponMapper;
use WechatPayBusifavorBundle\Service\StockMapper;

#[WithMonologChannel(channel: 'wechat_pay_busifavor')]
class BusifavorService
{
    public function __construct(
        private readonly BusifavorClient $client,
        private readonly StockRepository $stockRepository,
        private readonly CouponRepository $couponRepository,
        private readonly StockMapper $stockMapper,
        private readonly CouponMapper $couponMapper,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 创建商家券批次
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     *
     * 并发控制：不考虑并发，由业务层面保证同一批次不会重复创建
     */
    #[Transactional]
    public function createStock(array $data): array
    {
        $request = new CreateStockRequest($data);

        try {
            $response = $this->client->request($request);
            assert(is_array($response));
            /** @var array<string, mixed> $response */
            $this->saveStockFromResponse($response, $data);

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error('创建商家券批次失败: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * @param array<string, mixed> $response
     * @param array<string, mixed> $originalData
     */
    private function saveStockFromResponse(array $response, array $originalData): void
    {
        if (!isset($response['stock_id'])) {
            return;
        }

        $mergedData = array_merge($originalData, $response);
        $stockData = StockData::fromArray($mergedData);
        $stock = $this->stockMapper->createFromData($stockData);
        $this->stockRepository->save($stock);
    }

    /**
     * 查询商家券批次详情
     *
     * @return array<string, mixed>
     *
     * 并发控制：不考虑并发，同步操作不会影响数据一致性
     */
    #[Transactional]
    public function getStock(string $stockId): array
    {
        $request = new GetStockRequest($stockId);

        try {
            $response = $this->client->request($request);
            assert(is_array($response));
            /** @var array<string, mixed> $response */
            $this->updateStockFromResponse($stockId, $response);

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error('查询商家券批次详情失败: ' . $e->getMessage(), [
                'stockId' => $stockId,
                'exception' => $e,
            ]);

            throw $e;
        }
    }

    /**
     * 不考虑并发，由上层方法保证事务一致性
     *
     * @param array<string, mixed> $response
     */
    private function updateStockFromResponse(string $stockId, array $response): void
    {
        $stock = $this->stockRepository->findByStockId($stockId);
        if (null === $stock || !isset($response['stock_id'])) {
            return;
        }

        $this->stockMapper->updateFromResponse($stock, $response);
        $this->stockRepository->save($stock);
    }

    /**
     * 核销商家券
     *
     * @return array<string, mixed>
     */
    #[Transactional]
    public function useCoupon(string $couponCode, string $stockId, string $appid, string $openid, string $useRequestNo): array
    {
        $data = [
            'coupon_code' => $couponCode,
            'stock_id' => $stockId,
            'appid' => $appid,
            'openid' => $openid,
            'use_request_no' => $useRequestNo,
        ];

        $request = new UseCouponRequest($data);

        try {
            $response = $this->client->request($request);

            // 更新数据库中的券信息
            $coupon = $this->couponRepository->findByCouponCode($couponCode);
            if (null !== $coupon) {
                $coupon->setStatus(CouponStatus::USED);
                $coupon->setUsedTime(new \DateTimeImmutable());
                $coupon->setUseRequestNo($useRequestNo);
                if (is_array($response)) {
                    /** @var array<string, mixed> $response */
                    $coupon->setUseInfo($response);
                }

                $this->couponRepository->save($coupon);
            }

            assert(is_array($response));

            /** @var array<string, mixed> $response */
            return $response;
        } catch (\Throwable $e) {
            $this->logger->error('核销商家券失败: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);

            throw $e;
        }
    }

    /**
     * 查询用户单张券详情
     *
     * @return array<string, mixed>
     */
    #[Transactional]
    public function getCoupon(string $couponCode, string $openid, string $appid): array
    {
        $request = new GetCouponRequest($couponCode, $openid, $appid);

        try {
            $response = $this->client->request($request);
            assert(is_array($response));
            /** @var array<string, mixed> $response */
            $this->syncSingleCouponToDatabase($couponCode, $openid, $response);

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error('查询用户单张券详情失败: ' . $e->getMessage(), [
                'couponCode' => $couponCode,
                'openid' => $openid,
                'appid' => $appid,
                'exception' => $e,
            ]);

            throw $e;
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    private function syncSingleCouponToDatabase(string $couponCode, string $openid, array $response): void
    {
        $coupon = $this->couponRepository->findByCouponCode($couponCode);

        if (null === $coupon && isset($response['coupon_code'])) {
            /** @var array<string, mixed> $response */
            $coupon = $this->couponMapper->createFromResponse($response, $openid);
            $this->couponRepository->save($coupon);
        } elseif (null !== $coupon) {
            /** @var array<string, mixed> $response */
            $this->couponMapper->updateFromResponse($coupon, $response);
            $this->couponRepository->save($coupon);
        }
    }

    /**
     * 查询用户券列表
     *
     * @return array<string, mixed>
     */
    #[Transactional]
    public function getUserCoupons(string $openid, string $appid, ?string $stockId = null, ?string $status = null, ?int $offset = null, ?int $limit = null): array
    {
        $request = new GetUserCouponsRequest($openid, $appid, $stockId, $status, $offset, $limit);

        try {
            $response = $this->client->request($request);
            assert(is_array($response));
            /** @var array<string, mixed> $response */
            $this->syncUserCouponsToDatabase($response, $openid);

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error('查询用户券列表失败: ' . $e->getMessage(), [
                'openid' => $openid,
                'appid' => $appid,
                'stockId' => $stockId,
                'status' => $status,
                'exception' => $e,
            ]);

            throw $e;
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    private function syncUserCouponsToDatabase(array $response, string $openid): void
    {
        if (!isset($response['data']) || !is_array($response['data'])) {
            return;
        }

        /** @var array<mixed> $data */
        $data = $response['data'];

        foreach ($data as $couponData) {
            if (is_array($couponData)) {
                /** @var array<string, mixed> $couponData */
                $this->syncCouponToDatabase($couponData, $openid);
            }
        }

        if (count($data) > 0) {
            $this->couponRepository->flush();
        }
    }

    /**
     * 获取本地数据库中的商家券批次
     *
     * 并发控制：不考虑并发，只读操作不会影响数据一致性
     */
    /**
     * @return array<Stock>
     */
    public function getLocalStocks(?string $status = null): array
    {
        if (null !== $status) {
            return $this->stockRepository->findStocksByStatus($status);
        }

        return $this->stockRepository->findBy([], ['createdTime' => 'DESC']);
    }

    /**
     * 获取本地数据库中的商家券
     */
    /**
     * @return array<Coupon>
     */
    public function getLocalCoupons(?string $stockId = null, ?string $openid = null): array
    {
        $criteria = [];

        if (null !== $stockId) {
            $criteria['stockId'] = $stockId;
        }

        if (null !== $openid) {
            $criteria['openid'] = $openid;
        }

        return $this->couponRepository->findBy($criteria, ['createdTime' => 'DESC']);
    }

    /**
     * @param array<string, mixed> $couponData
     */
    private function syncCouponToDatabase(array $couponData, string $openid): void
    {
        if (!isset($couponData['coupon_code']) || !isset($couponData['stock_id'])) {
            return;
        }

        $couponCode = isset($couponData['coupon_code']) && is_string($couponData['coupon_code']) ? $couponData['coupon_code'] : '';
        $coupon = $this->couponRepository->findByCouponCode($couponCode);

        if (null === $coupon) {
            /** @var array<string, mixed> $couponData */
            $coupon = $this->couponMapper->createFromResponse($couponData, $openid);
        } else {
            /** @var array<string, mixed> $couponData */
            $this->couponMapper->updateFromResponse($coupon, $couponData);
        }

        $this->couponRepository->save($coupon, false);
    }
}
