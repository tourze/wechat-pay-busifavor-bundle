<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Entity\Stock;

#[AdminDashboard(routePath: '/busifavor/admin', routeName: 'busifavor_admin')]
final class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('@EasyAdmin/welcome.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('微信商家券管理')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('商家券批次', 'fa fa-list', Stock::class);
        yield MenuItem::linkToCrud('商家券', 'fa fa-ticket', Coupon::class);
    }
}
