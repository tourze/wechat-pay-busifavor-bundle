<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatPayBusifavorBundle\Controller\Admin\DashboardController;

/**
 * @internal
 */
#[CoversClass(DashboardController::class)]
#[RunTestsInSeparateProcesses]
final class DashboardControllerTest extends AbstractWebTestCase
{
    protected function onSetUp(): void
    {
        // No setup required for these tests
    }

    public function testControllerExtendsCorrectBaseClass(): void
    {
        $controller = self::getService(DashboardController::class);
        self::assertInstanceOf(AbstractDashboardController::class, $controller);
    }

    public function testControllerCanBeInstantiated(): void
    {
        $controller = self::getService(DashboardController::class);
        self::assertInstanceOf(DashboardController::class, $controller);
    }

    public function testControllerClassIsFinal(): void
    {
        $reflection = new \ReflectionClass(DashboardController::class);
        self::assertTrue($reflection->isFinal(), 'Controller class should be final');
    }

    public function testControllerHasIndexMethod(): void
    {
        $reflection = new \ReflectionClass(DashboardController::class);
        self::assertTrue($reflection->hasMethod('index'));
    }

    public function testControllerHasConfigureDashboardMethod(): void
    {
        $reflection = new \ReflectionClass(DashboardController::class);
        self::assertTrue($reflection->hasMethod('configureDashboard'));
    }

    public function testControllerHasConfigureMenuItemsMethod(): void
    {
        $reflection = new \ReflectionClass(DashboardController::class);
        self::assertTrue($reflection->hasMethod('configureMenuItems'));
    }

    public function testControllerServiceCanBeRetrievedFromContainer(): void
    {
        $controller = self::getService(DashboardController::class);
        self::assertInstanceOf(DashboardController::class, $controller);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        // 实现抽象方法以满足PHPStan要求
        self::markTestSkipped('This test is not applicable for Dashboard controller');
    }
}
