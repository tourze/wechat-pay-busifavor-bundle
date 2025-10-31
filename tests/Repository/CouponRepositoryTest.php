<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Enum\CouponStatus;
use WechatPayBusifavorBundle\Repository\CouponRepository;

/**
 * @template-extends AbstractRepositoryTestCase<Coupon>
 * @internal
 */
#[CoversClass(CouponRepository::class)]
#[RunTestsInSeparateProcesses]
final class CouponRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // AbstractRepositoryTestCase 会自动加载 DataFixtures，无需手动清理
    }

    /**
     * 测试可以获取仓库实例
     */
    public function testCanGetRepositoryInstance(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);
    }

    /**
     * 测试仓库定义了必要的方法
     */
    public function testRepositoryHasRequiredMethods(): void
    {
        $methods = get_class_methods(CouponRepository::class);

        $requiredMethods = [
            'findByCouponCode',
            'findByOpenid',
            'findByStockId',
            'findAvailableCouponsByOpenid',
            'findAvailableCouponsByStockId',
            'countCouponsByStockId',
            'countAvailableCouponsByStockId',
        ];

        foreach ($requiredMethods as $method) {
            $this->assertContains($method, $methods, "Method {$method} should exist in CouponRepository");
        }
    }

    public function testCountAvailableCouponsByStockId(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);
        $result = $repository->countAvailableCouponsByStockId('test-stock');
        $this->assertIsInt($result);
    }

    public function testCountCouponsByStockId(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);
        $result = $repository->countCouponsByStockId('test-stock');
        $this->assertIsInt($result);
    }

    public function testFindAvailableCouponsByOpenid(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);
        $result = $repository->findAvailableCouponsByOpenid('test-openid');
        $this->assertIsArray($result);
    }

    public function testFindAvailableCouponsByStockId(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);
        $result = $repository->findAvailableCouponsByStockId('test-stock');
        $this->assertIsArray($result);
    }

    public function testFindByCouponCode(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);
        $result = $repository->findByCouponCode('test-coupon');
        $this->assertNull($result);
    }

    public function testFindByOpenid(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);
        $result = $repository->findByOpenid('test-openid');
        $this->assertIsArray($result);
    }

    public function testFindByStockId(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);
        $result = $repository->findByStockId('test-stock');
        $this->assertIsArray($result);
    }

    public function testSave(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        $coupon = new Coupon();
        $coupon->setCouponCode('test-code');
        $coupon->setStockId('test-stock');
        $coupon->setOpenid('test-openid');

        $repository->save($coupon, false);
        $this->assertTrue(self::getEntityManager()->contains($coupon));
    }

    public function testRemove(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        $coupon = new Coupon();
        $coupon->setCouponCode('test-code');
        $coupon->setStockId('test-stock');
        $coupon->setOpenid('test-openid');

        self::getEntityManager()->persist($coupon);
        self::getEntityManager()->flush();

        $repository->remove($coupon, false);
        $this->assertFalse(self::getEntityManager()->contains($coupon));
    }

    /**
     * 测试count方法正常工作
     */
    public function testCountWithValidCriteriaShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加测试数据
        $coupon = new Coupon();
        $coupon->setCouponCode('test-code');
        $coupon->setStockId('test-stock');
        $coupon->setOpenid('test-openid');
        $this->persistAndFlush($coupon);

        $count = $repository->count(['stockId' => 'test-stock']);
        $this->assertSame(1, $count);
    }

    /**
     * 测试findBy方法正常工作
     */
    public function testFindByWithValidCriteriaShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加测试数据
        $coupon = new Coupon();
        $coupon->setCouponCode('test-code');
        $coupon->setStockId('test-stock');
        $coupon->setOpenid('test-openid');
        $this->persistAndFlush($coupon);

        $results = $repository->findBy(['stockId' => 'test-stock']);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(Coupon::class, $results[0]);
        $this->assertSame('test-code', $results[0]->getCouponCode());
    }

    /**
     * 测试findBy方法在数据库不可用时抛出异常
     */

    /**
     * 测试findBy方法使用不存在的字段时抛出异常
     */

    /**
     * 测试findBy方法应该尊重limit和offset参数
     */

    /**
     * 测试findBy方法使用匹配条件时返回实体数组
     */

    /**
     * 测试findBy方法应该尊重orderBy子句
     */

    /**
     * 测试findOneBy方法使用匹配条件时返回实体
     */

    /**
     * 测试findAll方法正常工作
     */
    public function testFindAllShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        $results = $repository->findAll();
        $this->assertIsArray($results);
        // DataFixtures 加载了 3 个优惠券，所有结果应该大于等于 3
        $this->assertGreaterThanOrEqual(3, count($results));

        // 验证返回的都是 Coupon 实体
        foreach ($results as $result) {
            $this->assertInstanceOf(Coupon::class, $result);
        }
    }

    /**
     * 测试findAll方法在数据库不可用时抛出异常
     */

    /**
     * 测试findAll方法在没有记录时返回空数组
     */

    /**
     * 测试findAll方法在有记录时返回实体数组
     */

    /**
     * 测试find方法正常工作
     */
    public function testFindShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加测试数据
        $coupon = new Coupon();
        $coupon->setCouponCode('test-code');
        $coupon->setStockId('test-stock');
        $coupon->setOpenid('test-openid');
        $this->persistAndFlush($coupon);

        $id = $coupon->getId();
        $result = $repository->find($id);
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertSame($id, $result->getId());
    }

    /**
     * 测试find方法使用存在的ID返回实体
     */

    /**
     * 测试findOneBy方法应该支持排序
     */
    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加测试数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-z');
        $coupon1->setStockId('same-stock');
        $coupon1->setOpenid('openid-1');
        $this->persistAndFlush($coupon1);

        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-a');
        $coupon2->setStockId('same-stock');
        $coupon2->setOpenid('openid-2');
        $this->persistAndFlush($coupon2);

        // 使用排序获取第一个匹配的结果
        $result = $repository->findOneBy(['stockId' => 'same-stock'], ['couponCode' => 'ASC']);
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertSame('code-a', $result->getCouponCode());

        // 使用降序排序
        $result = $repository->findOneBy(['stockId' => 'same-stock'], ['couponCode' => 'DESC']);
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertSame('code-z', $result->getCouponCode());
    }

    /**
     * 测试使用IS NULL查询可空字段openid
     */
    public function testFindByWithNullOpenidShouldReturnMatchingEntities(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有openid的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('test-openid');
        $this->persistAndFlush($coupon1);

        // 添加没有openid的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid(null);
        $this->persistAndFlush($coupon2);

        // 查询openid为null的数据
        $results = $repository->findBy(['openid' => null]);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(Coupon::class, $results[0]);
        $this->assertSame('code-2', $results[0]->getCouponCode());
        $this->assertNull($results[0]->getOpenid());
    }

    /**
     * 测试使用IS NULL查询可空字段transactionId
     */
    public function testFindByWithNullTransactionIdShouldReturnMatchingEntities(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有transactionId的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setTransactionId('tx-123');
        $this->persistAndFlush($coupon1);

        // 添加没有transactionId的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setTransactionId(null);
        $this->persistAndFlush($coupon2);

        // 查询transactionId为null的数据（包括DataFixtures中的null数据）
        $results = $repository->findBy(['transactionId' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        // 验证至少包含我们测试添加的数据
        $couponCodes = array_map(fn ($coupon) => $coupon->getCouponCode(), $results);
        $this->assertContains('code-2', $couponCodes);

        // 验证返回的数据都有null的transactionId
        foreach ($results as $result) {
            $this->assertInstanceOf(Coupon::class, $result);
            $this->assertNull($result->getTransactionId());
        }
    }

    /**
     * 测试count方法使用IS NULL查询可空字段
     */
    public function testCountWithNullOpenidShouldReturnCorrectNumber(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有openid的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('test-openid');
        $this->persistAndFlush($coupon1);

        // 添加没有openid的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid(null);
        $this->persistAndFlush($coupon2);

        $coupon3 = new Coupon();
        $coupon3->setCouponCode('code-3');
        $coupon3->setStockId('stock-3');
        $coupon3->setOpenid(null);
        $this->persistAndFlush($coupon3);

        // 统计openid为null的数据
        $count = $repository->count(['openid' => null]);
        $this->assertSame(2, $count);
    }

    /**
     * 测试使用IS NULL查询可空字段usedTime
     */
    public function testFindByWithNullUsedTimeShouldReturnMatchingEntities(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有usedTime的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setUsedTime(new \DateTimeImmutable());
        $this->persistAndFlush($coupon1);

        // 添加没有usedTime的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setUsedTime(null);
        $this->persistAndFlush($coupon2);

        // 查询usedTime为null的数据（包括DataFixtures中的null数据）
        $results = $repository->findBy(['usedTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        // 验证至少包含我们测试添加的数据
        $couponCodes = array_map(fn ($coupon) => $coupon->getCouponCode(), $results);
        $this->assertContains('code-2', $couponCodes);

        // 验证返回的数据都有null的usedTime
        foreach ($results as $result) {
            $this->assertInstanceOf(Coupon::class, $result);
            $this->assertNull($result->getUsedTime());
        }
    }

    /**
     * 测试使用IS NULL查询可空字段expiryTime
     */
    public function testFindByWithNullExpiryTimeShouldReturnMatchingEntities(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有expiryTime的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setExpiryTime(new \DateTimeImmutable());
        $this->persistAndFlush($coupon1);

        // 添加没有expiryTime的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setExpiryTime(null);
        $this->persistAndFlush($coupon2);

        // 查询expiryTime为null的数据（包括DataFixtures中的null数据）
        $results = $repository->findBy(['expiryTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        // 验证至少包含我们测试添加的数据
        $couponCodes = array_map(fn ($coupon) => $coupon->getCouponCode(), $results);
        $this->assertContains('code-2', $couponCodes);

        // 验证返回的数据都有null的expiryTime
        foreach ($results as $result) {
            $this->assertInstanceOf(Coupon::class, $result);
            $this->assertNull($result->getExpiryTime());
        }
    }

    /**
     * 测试使用IS NULL查询可空字段useRequestNo
     */
    public function testFindByWithNullUseRequestNoShouldReturnMatchingEntities(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有useRequestNo的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setUseRequestNo('req-123');
        $this->persistAndFlush($coupon1);

        // 添加没有useRequestNo的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setUseRequestNo(null);
        $this->persistAndFlush($coupon2);

        // 查询useRequestNo为null的数据（包括DataFixtures中的null数据）
        $results = $repository->findBy(['useRequestNo' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        // 验证至少包含我们测试添加的数据
        $couponCodes = array_map(fn ($coupon) => $coupon->getCouponCode(), $results);
        $this->assertContains('code-2', $couponCodes);

        // 验证返回的数据都有null的useRequestNo
        foreach ($results as $result) {
            $this->assertInstanceOf(Coupon::class, $result);
            $this->assertNull($result->getUseRequestNo());
        }
    }

    /**
     * 测试使用IS NULL查询可空字段useInfo
     */
    public function testFindByWithNullUseInfoShouldReturnMatchingEntities(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有useInfo的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setUseInfo(['key' => 'value']);
        $this->persistAndFlush($coupon1);

        // 添加没有useInfo的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setUseInfo(null);
        $this->persistAndFlush($coupon2);

        // 查询useInfo为null的数据（包括DataFixtures中的null数据）
        $results = $repository->findBy(['useInfo' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        // 验证至少包含我们测试添加的数据
        $couponCodes = array_map(fn ($coupon) => $coupon->getCouponCode(), $results);
        $this->assertContains('code-2', $couponCodes);

        // 验证返回的数据都有null的useInfo
        foreach ($results as $result) {
            $this->assertInstanceOf(Coupon::class, $result);
            $this->assertNull($result->getUseInfo());
        }
    }

    /**
     * 测试count方法使用IS NULL查询可空字段transactionId
     */
    public function testCountWithNullTransactionIdShouldReturnCorrectNumber(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有transactionId的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setTransactionId('tx-123');
        $this->persistAndFlush($coupon1);

        // 添加没有transactionId的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setTransactionId(null);
        $this->persistAndFlush($coupon2);

        $coupon3 = new Coupon();
        $coupon3->setCouponCode('code-3');
        $coupon3->setStockId('stock-3');
        $coupon3->setOpenid('openid-3');
        $coupon3->setTransactionId(null);
        $this->persistAndFlush($coupon3);

        // 统计transactionId为null的数据（包括DataFixtures中的null数据）
        $count = $repository->count(['transactionId' => null]);
        $this->assertGreaterThanOrEqual(2, $count);
    }

    /**
     * 测试count方法使用IS NULL查询可空字段usedTime
     */
    public function testCountWithNullUsedTimeShouldReturnCorrectNumber(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有usedTime的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setUsedTime(new \DateTimeImmutable());
        $this->persistAndFlush($coupon1);

        // 添加没有usedTime的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setUsedTime(null);
        $this->persistAndFlush($coupon2);

        $coupon3 = new Coupon();
        $coupon3->setCouponCode('code-3');
        $coupon3->setStockId('stock-3');
        $coupon3->setOpenid('openid-3');
        $coupon3->setUsedTime(null);
        $this->persistAndFlush($coupon3);

        // 统计usedTime为null的数据（包括DataFixtures中的null数据）
        $count = $repository->count(['usedTime' => null]);
        $this->assertGreaterThanOrEqual(2, $count);
    }

    /**
     * 测试count方法使用IS NULL查询可空字段expiryTime
     */
    public function testCountWithNullExpiryTimeShouldReturnCorrectNumber(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有expiryTime的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setExpiryTime(new \DateTimeImmutable());
        $this->persistAndFlush($coupon1);

        // 添加没有expiryTime的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setExpiryTime(null);
        $this->persistAndFlush($coupon2);

        $coupon3 = new Coupon();
        $coupon3->setCouponCode('code-3');
        $coupon3->setStockId('stock-3');
        $coupon3->setOpenid('openid-3');
        $coupon3->setExpiryTime(null);
        $this->persistAndFlush($coupon3);

        // 统计expiryTime为null的数据（包括DataFixtures中的null数据）
        $count = $repository->count(['expiryTime' => null]);
        $this->assertGreaterThanOrEqual(2, $count);
    }

    /**
     * 测试count方法使用IS NULL查询可空字段useRequestNo
     */
    public function testCountWithNullUseRequestNoShouldReturnCorrectNumber(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有useRequestNo的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setUseRequestNo('req-123');
        $this->persistAndFlush($coupon1);

        // 添加没有useRequestNo的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setUseRequestNo(null);
        $this->persistAndFlush($coupon2);

        $coupon3 = new Coupon();
        $coupon3->setCouponCode('code-3');
        $coupon3->setStockId('stock-3');
        $coupon3->setOpenid('openid-3');
        $coupon3->setUseRequestNo(null);
        $this->persistAndFlush($coupon3);

        // 统计useRequestNo为null的数据（包括DataFixtures中的null数据）
        $count = $repository->count(['useRequestNo' => null]);
        $this->assertGreaterThanOrEqual(2, $count);
    }

    /**
     * 测试count方法使用IS NULL查询可空字段useInfo
     */
    public function testCountWithNullUseInfoShouldReturnCorrectNumber(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加有useInfo的数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-1');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid('openid-1');
        $coupon1->setUseInfo(['key' => 'value']);
        $this->persistAndFlush($coupon1);

        // 添加没有useInfo的数据
        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-2');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid('openid-2');
        $coupon2->setUseInfo(null);
        $this->persistAndFlush($coupon2);

        $coupon3 = new Coupon();
        $coupon3->setCouponCode('code-3');
        $coupon3->setStockId('stock-3');
        $coupon3->setOpenid('openid-3');
        $coupon3->setUseInfo(null);
        $this->persistAndFlush($coupon3);

        // 统计useInfo为null的数据（包括DataFixtures中的null数据）
        $count = $repository->count(['useInfo' => null]);
        $this->assertGreaterThanOrEqual(2, $count);
    }

    /**
     * 测试使用IS NULL查询可空字段时的排序逻辑
     */

    /**
     * 测试findOneBy查询openid为null的实体
     */

    /**
     * 测试findBy查询openid为null的所有实体
     */

    /**
     * 测试count查询openid为null的正确数量
     */

    /**
     * 测试findOneBy方法使用null值进行排序查询
     */
    public function testFindOneByWithNullValueAndOrderByShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加测试数据
        $coupon1 = new Coupon();
        $coupon1->setCouponCode('code-z');
        $coupon1->setStockId('stock-1');
        $coupon1->setOpenid(null);
        $this->persistAndFlush($coupon1);

        $coupon2 = new Coupon();
        $coupon2->setCouponCode('code-a');
        $coupon2->setStockId('stock-2');
        $coupon2->setOpenid(null);
        $this->persistAndFlush($coupon2);

        // 使用排序获取第一个null openid的结果
        $result = $repository->findOneBy(['openid' => null], ['couponCode' => 'ASC']);
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertSame('code-a', $result->getCouponCode());
        $this->assertNull($result->getOpenid());
    }

    protected function createNewEntity(): object
    {
        $entity = new Coupon();

        // 设置基本字段
        $entity->setCouponCode('TEST_' . uniqid());
        $entity->setStockId('stock_' . uniqid());
        $entity->setOpenid('user_' . uniqid() . '@example.com');
        $entity->setStatus(CouponStatus::SENDED);
        $entity->setExpiryTime(new \DateTimeImmutable('+30 days'));

        return $entity;
    }

    /**
     * 测试flush方法正常工作
     */
    public function testFlushShouldWork(): void
    {
        $repository = self::getEntityManager()->getRepository(Coupon::class);
        $this->assertInstanceOf(CouponRepository::class, $repository);

        // 添加测试数据但不立即flush
        $coupon = new Coupon();
        $coupon->setCouponCode('test-flush-code');
        $coupon->setStockId('test-flush-stock');
        $coupon->setOpenid('test-flush-openid');

        self::getEntityManager()->persist($coupon);

        // 验证flush前数据尚未提交
        $beforeFlushCount = $repository->count(['couponCode' => 'test-flush-code']);
        $this->assertSame(0, $beforeFlushCount);

        // 执行flush
        $repository->flush();

        // 验证flush后数据已提交
        $afterFlushCount = $repository->count(['couponCode' => 'test-flush-code']);
        $this->assertSame(1, $afterFlushCount);
    }

    protected function getRepository(): CouponRepository
    {
        return self::getService(CouponRepository::class);
    }
}
