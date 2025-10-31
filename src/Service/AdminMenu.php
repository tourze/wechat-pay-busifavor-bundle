<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SectionMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatPayBusifavorBundle\Controller\Admin\CouponCrudController;
use WechatPayBusifavorBundle\Controller\Admin\StockCrudController;

/**
 * 微信支付商家券管理菜单服务
 * 为EasyAdmin提供商家券相关的管理菜单项
 */
#[Autoconfigure(public: true)]
class AdminMenu implements MenuProviderInterface
{
    public function __invoke(ItemInterface $item): void
    {
        // 实现接口要求的方法
        // 在实际应用中，这里会添加菜单项到 $item 中
        // 为了保持向后兼容，这里保留 getMenuItems() 方法
    }

    /**
     * 获取微信支付商家券管理模块的菜单项
     * @return array<int, CrudMenuItem|SectionMenuItem>
     */
    public function getMenuItems(): array
    {
        return [
            MenuItem::section('微信支付商家券', 'fas fa-ticket-alt'),
            MenuItem::linkToCrud('批次管理', 'fas fa-layer-group', StockCrudController::getEntityFqcn())
                ->setController(StockCrudController::class),
            MenuItem::linkToCrud('券管理', 'fas fa-tags', CouponCrudController::getEntityFqcn())
                ->setController(CouponCrudController::class),
        ];
    }
}
