<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Client;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatPayBusifavorBundle\Client\BusifavorClient;

/**
 * @internal
 */
#[CoversClass(BusifavorClient::class)]
#[RunTestsInSeparateProcesses]
final class BusifavorClientTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testClientCanBeRetrievedFromContainer(): void
    {
        $client = self::getService(BusifavorClient::class);
        $this->assertInstanceOf(BusifavorClient::class, $client);
    }
}
