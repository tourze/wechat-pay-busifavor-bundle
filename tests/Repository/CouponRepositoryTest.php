<?php

namespace WechatPayBusifavorBundle\Tests\Repository;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Repository\CouponRepository;

class CouponRepositoryTest extends TestCase
{
    /**
     * 测试仓库类存在且可实例化
     */
    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(CouponRepository::class));
    }

    /**
     * 测试仓库继承了正确的基类
     */
    public function testRepositoryExtendsServiceEntityRepository(): void
    {
        $reflection = new \ReflectionClass(CouponRepository::class);
        $parent = $reflection->getParentClass();
        
        $this->assertNotFalse($parent);
        $this->assertEquals('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $parent->getName());
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
            $this->assertContains($method, $methods, "Method $method should exist in CouponRepository");
        }
    }
}