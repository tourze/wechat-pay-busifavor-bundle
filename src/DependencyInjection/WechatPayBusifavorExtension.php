<?php

namespace WechatPayBusifavorBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class WechatPayBusifavorExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
