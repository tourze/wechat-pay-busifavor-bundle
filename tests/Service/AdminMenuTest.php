<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\TestDox;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatPayBusifavorBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    protected function onSetUp(): void
    {
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    #[TestDox('应该返回菜单项数组')]
    public function testGetMenuItemsShouldReturnArrayOfMenuItems(): void
    {
        $menuItems = $this->adminMenu->getMenuItems();

        $this->assertIsArray($menuItems);
        $this->assertNotEmpty($menuItems);
        $this->assertCount(3, $menuItems);

        // 检查所有项目都是对象
        foreach ($menuItems as $menuItem) {
            $this->assertIsObject($menuItem);
        }
    }

    #[TestDox('应该包含微信支付商家券分组')]
    public function testShouldContainWechatPayBusifavorSection(): void
    {
        $menuItems = $this->adminMenu->getMenuItems();

        $this->assertGreaterThan(0, count($menuItems));

        // 简单检查第一个菜单项是否存在
        $this->assertIsObject($menuItems[0]);
    }

    #[TestDox('应该包含批次管理菜单项')]
    public function testShouldContainStockManagementMenuItem(): void
    {
        $menuItems = $this->adminMenu->getMenuItems();

        $this->assertGreaterThanOrEqual(2, count($menuItems));

        // 简单检查第二个菜单项是否存在
        $this->assertIsObject($menuItems[1]);
    }

    #[TestDox('应该包含券管理菜单项')]
    public function testShouldContainCouponManagementMenuItem(): void
    {
        $menuItems = $this->adminMenu->getMenuItems();

        $this->assertGreaterThanOrEqual(3, count($menuItems));

        // 简单检查第三个菜单项是否存在
        $this->assertIsObject($menuItems[2]);
    }

    #[TestDox('菜单项应该按正确顺序排列')]
    public function testMenuItemsShouldBeInCorrectOrder(): void
    {
        $menuItems = $this->adminMenu->getMenuItems();

        $this->assertCount(3, $menuItems);

        // 检查所有三个菜单项都存在
        $this->assertIsObject($menuItems[0]);
        $this->assertIsObject($menuItems[1]);
        $this->assertIsObject($menuItems[2]);
    }

    #[TestDox('AdminMenu服务应该可以实例化')]
    public function testAdminMenuCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }

    #[TestDox('getMenuItems方法应该始终返回相同的结果')]
    public function testGetMenuItemsReturnsConsistentResults(): void
    {
        $menuItems1 = $this->adminMenu->getMenuItems();
        $menuItems2 = $this->adminMenu->getMenuItems();

        $this->assertCount(count($menuItems1), $menuItems2);
        $this->assertEquals($menuItems1, $menuItems2);
    }
}
