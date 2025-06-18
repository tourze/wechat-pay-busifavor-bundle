<?php

namespace WechatPayBusifavorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class WechatPayBusifavorBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class => ['all' => true],
            \HttpClientBundle\HttpClientBundle::class => ['all' => true],
            \WechatPayBundle\WechatPayBundle::class => ['all' => true],
        ];
    }
}
