<?php

namespace WechatPayBusifavorBundle\Tests\Integration;

use HttpClientBundle\HttpClientBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use WechatPayBundle\WechatPayBundle;
use WechatPayBusifavorBundle\WechatPayBusifavorBundle;

class IntegrationTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineIndexedBundle(),
            new HttpClientBundle(),
            new WechatPayBundle(),
            new WechatPayBusifavorBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test' => true,
            'secret' => 'test',
        ]);

        // 加载服务配置
        $container->import('../../../src/Resources/config/services.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // 路由配置（如果需要）
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/cache/' . spl_object_hash($this);
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/logs/' . spl_object_hash($this);
    }
} 