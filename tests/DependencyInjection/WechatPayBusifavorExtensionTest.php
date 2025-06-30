<?php

namespace WechatPayBusifavorBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatPayBusifavorBundle\DependencyInjection\WechatPayBusifavorExtension;

class WechatPayBusifavorExtensionTest extends TestCase
{
    private WechatPayBusifavorExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatPayBusifavorExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $this->extension->load([], $this->container);

        // 验证服务是否被加载
        $this->assertTrue($this->container->hasDefinition('wechat_pay_busifavor.service.busifavor'));
        $this->assertTrue($this->container->hasDefinition('wechat_pay_busifavor.repository.stock'));
        $this->assertTrue($this->container->hasDefinition('wechat_pay_busifavor.repository.coupon'));
        $this->assertTrue($this->container->hasDefinition('wechat_pay_busifavor.command.list_user_coupons'));
        $this->assertTrue($this->container->hasDefinition('wechat_pay_busifavor.command.sync_stock'));
    }

    public function testServicesArePublic(): void
    {
        $this->extension->load([], $this->container);

        $busifavorService = $this->container->getDefinition('wechat_pay_busifavor.service.busifavor');
        $this->assertTrue($busifavorService->isPublic());
    }

    public function testGetAlias(): void
    {
        $this->assertEquals('wechat_pay_busifavor', $this->extension->getAlias());
    }
}