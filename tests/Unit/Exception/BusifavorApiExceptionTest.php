<?php

namespace WechatPayBusifavorBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use WechatPayBusifavorBundle\Exception\BusifavorApiException;

class BusifavorApiExceptionTest extends TestCase
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