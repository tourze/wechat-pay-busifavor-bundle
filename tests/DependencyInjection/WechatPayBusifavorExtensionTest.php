<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatPayBusifavorBundle\DependencyInjection\WechatPayBusifavorExtension;

/**
 * @internal
 */
#[CoversClass(WechatPayBusifavorExtension::class)]
final class WechatPayBusifavorExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testServicesArePublic(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new WechatPayBusifavorExtension();

        $configs = [];
        $extension->load($configs, $container);

        $busifavorService = $container->getDefinition('WechatPayBusifavorBundle\Service\BusifavorService');
        $this->assertFalse($busifavorService->isPublic()); // 服务默认是私有的，这是正确行为
    }

    public function testGetAlias(): void
    {
        $extension = new WechatPayBusifavorExtension();
        $this->assertEquals('wechat_pay_busifavor', $extension->getAlias());
    }

    protected function setUp(): void
    {
        parent::setUp();
        // 测试 Extension 不需要特殊的设置
    }
}
