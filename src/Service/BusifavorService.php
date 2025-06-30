<?php

namespace WechatPayBusifavorBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;
use WechatPayBusifavorBundle\Client\BusifavorClient;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\CouponStatus;
use WechatPayBusifavorBundle\Enum\StockStatus;
use WechatPayBusifavorBundle\Repository\CouponRepository;
use WechatPayBusifavorBundle\Repository\StockRepository;
use WechatPayBusifavorBundle\Request\CreateStockRequest;
use WechatPayBusifavorBundle\Request\GetCouponRequest;
use WechatPayBusifavorBundle\Request\GetStockRequest;
use WechatPayBusifavorBundle\Request\GetUserCouponsRequest;
use WechatPayBusifavorBundle\Request\UseCouponRequest;

class BusifavorService
{
    public function __construct(
        private readonly BusifavorClient $client,
        private readonly StockRepository $stockRepository,
        private readonly CouponRepository $couponRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 创建商家券批次
     */
    #[Transactional]
    public function createStock(array $data): array
    {
        // 组装请求数据
        $request = new CreateStockRequest($data);

        try {
            // 调用微信支付API
            $response = $this->client->request($request);

            // 保存到数据库
            if (isset($response['stock_id'])) {
                $stock = new Stock();
                $stock->setStockId($response['stock_id']);
                $stock->setStockName($data['stock_name']);
                $stock->setDescription($data['comment'] ?? null);
                $stock->setAvailableBeginTime($data['available_begin_time'] ?? []);
                $stock->setAvailableEndTime($data['available_end_time'] ?? []);
                $stock->setStockUseRule($data['stock_use_rule'] ?? []);
                $stock->setCouponUseRule($data['coupon_use_rule'] ?? []);
                $stock->setCustomEntrance($data['custom_entrance'] ?? []);
                $stock->setDisplayPatternInfo($data['display_pattern_info'] ?? []);
                $stock->setNotifyConfig($data['notify_config'] ?? null);
                $stock->setMaxCoupons($data['stock_use_rule']['max_coupons'] ?? 0);
                $stock->setMaxCouponsPerUser($data['stock_use_rule']['max_coupons_per_user'] ?? 0);
                $stock->setMaxAmount($data['stock_use_rule']['max_amount'] ?? 0);
                $stock->setMaxAmountByDay($data['stock_use_rule']['max_amount_by_day'] ?? 0);
                $stock->setNoLimit(isset($data['no_limit']) && $data['no_limit']);

                $this->entityManager->persist($stock);
                $this->entityManager->flush();
            }

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
     * 查询商家券批次详情
     */
    #[Transactional]
    public function getStock(string $stockId): array
    {
        $request = new GetStockRequest($stockId);

        try {
            $response = $this->client->request($request);

            // 更新数据库中的批次信息
            $stock = $this->stockRepository->findByStockId($stockId);
            if ($stock !== null && isset($response['stock_id'])) {
                if (isset($response['stock_state'])) {
                    $stock->setStatus(StockStatus::from($response['stock_state']));
                } elseif (isset($response['status'])) {
                    $stock->setStatus(StockStatus::from($response['status']));
                }
                if (isset($response['stock_name'])) {
                    $stock->setStockName($response['stock_name']);
                }
                if (isset($response['available_begin_time'])) {
                    $stock->setAvailableBeginTime(['value' => $response['available_begin_time']]);
                }
                if (isset($response['available_end_time'])) {
                    $stock->setAvailableEndTime(['value' => $response['available_end_time']]);
                }
                if (isset($response['stock_use_rule'])) {
                    $stock->setStockUseRule($response['stock_use_rule']);
                }
                if (isset($response['coupon_use_rule'])) {
                    $stock->setCouponUseRule($response['coupon_use_rule']);
                }
                if (isset($response['custom_entrance'])) {
                    $stock->setCustomEntrance($response['custom_entrance']);
                }
                if (isset($response['display_pattern_info'])) {
                    $stock->setDisplayPatternInfo($response['display_pattern_info']);
                }

                $this->entityManager->persist($stock);
                $this->entityManager->flush();
            }

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
     * 核销商家券
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
            if ($coupon !== null) {
                $coupon->setStatus(CouponStatus::USED);
                $coupon->setUsedTime(new \DateTimeImmutable());
                $coupon->setUseRequestNo($useRequestNo);
                $coupon->setUseInfo($response);

                $this->entityManager->persist($coupon);
                $this->entityManager->flush();
            }

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
     */
    #[Transactional]
    public function getCoupon(string $couponCode, string $openid, string $appid): array
    {
        $request = new GetCouponRequest($couponCode, $openid, $appid);

        try {
            $response = $this->client->request($request);

            // 确保数据库中有此券
            $coupon = $this->couponRepository->findByCouponCode($couponCode);
            if ($coupon === null && isset($response['coupon_code'])) {
                $coupon = new Coupon();
                $coupon->setCouponCode($response['coupon_code']);
                $coupon->setStockId($response['stock_id']);
                $coupon->setOpenid($openid);
                $coupon->setStatus(CouponStatus::from($response['status'] ?? CouponStatus::SENDED->value));

                if (isset($response['expire_time'])) {
                    $timestamp = strtotime($response['expire_time']);
                    if (false !== $timestamp) {
                        $coupon->setExpiryTime(new \DateTimeImmutable('@' . $timestamp));
                    }
                }

                $this->entityManager->persist($coupon);
                $this->entityManager->flush();
            } elseif ($coupon !== null && isset($response['status'])) {
                // 更新状态
                $coupon->setStatus(CouponStatus::from($response['status']));

                if (isset($response['use_time'])) {
                    $timestamp = strtotime($response['use_time']);
                    if (false !== $timestamp) {
                        $coupon->setUsedTime(new \DateTimeImmutable('@' . $timestamp));
                    }
                }

                $this->entityManager->persist($coupon);
                $this->entityManager->flush();
            }

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
     * 查询用户券列表
     */
    #[Transactional]
    public function getUserCoupons(string $openid, string $appid, ?string $stockId = null, ?string $status = null, ?int $offset = null, ?int $limit = null): array
    {
        $request = new GetUserCouponsRequest($openid, $appid, $stockId, $status, $offset, $limit);

        try {
            $response = $this->client->request($request);

            // 同步到数据库
            if (isset($response['data']) && is_array($response['data'])) {
                foreach ($response['data'] as $couponData) {
                    if (!isset($couponData['coupon_code']) || !isset($couponData['stock_id'])) {
                        continue;
                    }

                    $couponCode = $couponData['coupon_code'];
                    $coupon = $this->couponRepository->findByCouponCode($couponCode);

                    if ($coupon === null) {
                        $coupon = new Coupon();
                        $coupon->setCouponCode($couponCode);
                        $coupon->setStockId($couponData['stock_id']);
                        $coupon->setOpenid($openid);
                    }

                    $coupon->setStatus(CouponStatus::from($couponData['status'] ?? CouponStatus::SENDED->value));

                    if (isset($couponData['expire_time'])) {
                        $timestamp = strtotime($couponData['expire_time']);
                        if (false !== $timestamp) {
                            $coupon->setExpiryTime(new \DateTimeImmutable('@' . $timestamp));
                        }
                    }

                    if (isset($couponData['use_time'])) {
                        $timestamp = strtotime($couponData['use_time']);
                        if (false !== $timestamp) {
                            $coupon->setUsedTime(new \DateTimeImmutable('@' . $timestamp));
                        }
                    }

                    $this->entityManager->persist($coupon);
                }

                if (!empty($response['data'])) {
                    $this->entityManager->flush();
                }
            }

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
     * 获取本地数据库中的商家券批次
     */
    public function getLocalStocks(?string $status = null): array
    {
        if ($status !== null) {
            return $this->stockRepository->findStocksByStatus($status);
        }

        return $this->stockRepository->findBy([], ['createdTime' => 'DESC']);
    }

    /**
     * 获取本地数据库中的商家券
     */
    public function getLocalCoupons(?string $stockId = null, ?string $openid = null): array
    {
        $criteria = [];

        if ($stockId !== null) {
            $criteria['stockId'] = $stockId;
        }

        if ($openid !== null) {
            $criteria['openid'] = $openid;
        }

        return $this->couponRepository->findBy($criteria, ['createdTime' => 'DESC']);
    }
}
