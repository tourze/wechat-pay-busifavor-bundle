<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatPayBusifavorBundle\WechatPayBusifavorBundle;

/**
 * @internal
 */
#[CoversClass(WechatPayBusifavorBundle::class)] // @phpstan-ignore-line symplify.forbiddenExtendOfNonAbstractClass
#[RunTestsInSeparateProcesses]
final class WechatPayBusifavorBundleTest extends AbstractBundleTestCase
{
}
