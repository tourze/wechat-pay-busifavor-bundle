<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatPayBusifavorBundle\Exception\BusifavorApiException;

/**
 * @internal
 */
#[CoversClass(BusifavorApiException::class)]
final class BusifavorApiExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionIsThrowable(): void
    {
        $this->expectException(BusifavorApiException::class);
        $this->expectExceptionMessage('Test exception message');

        throw new BusifavorApiException('Test exception message');
    }

    public function testExceptionWithCode(): void
    {
        $this->expectException(BusifavorApiException::class);
        $this->expectExceptionMessage('Error occurred');
        $this->expectExceptionCode(500);

        throw new BusifavorApiException('Error occurred', 500);
    }

    public function testExceptionWithPreviousException(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new BusifavorApiException('Current exception', 0, $previous);

        $this->assertSame('Current exception', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionInheritance(): void
    {
        $exception = new BusifavorApiException('Test');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }
}
