<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatPayBusifavorBundle\Entity\Coupon;
use WechatPayBusifavorBundle\Enum\CouponStatus;

/**
 * @template-extends AbstractCrudController<Coupon>
 */
#[AdminCrud(routePath: '/busifavor/coupon', routeName: 'busifavor_coupon')]
final class CouponCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Coupon::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('商家券')
            ->setEntityLabelInPlural('商家券')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['couponCode', 'stockId', 'openid', 'transactionId'])
            ->setPaginatorPageSize(25)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('couponCode', '券码'))
            ->add(TextFilter::new('stockId', '批次ID'))
            ->add(TextFilter::new('openid', '用户OpenID'))
            ->add('status')
            ->add(DateTimeFilter::new('usedTime', '使用时间'))
            ->add(DateTimeFilter::new('expiryTime', '过期时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
            ->setSortable(true)
        ;

        yield TextField::new('couponCode', '券码')
            ->setRequired(true)
            ->setHelp('商家券唯一券码')
            ->setColumns(6)
        ;

        yield TextField::new('stockId', '批次ID')
            ->setRequired(true)
            ->setHelp('关联的商家券批次ID')
            ->setColumns(6)
        ;

        yield TextField::new('openid', '用户OpenID')
            ->setHelp('领取券的用户微信OpenID')
            ->setColumns(6)
        ;

        yield ChoiceField::new('status', '状态')
            ->setChoices([
                CouponStatus::SENDED->getLabel() => CouponStatus::SENDED,
                CouponStatus::USED->getLabel() => CouponStatus::USED,
                CouponStatus::EXPIRED->getLabel() => CouponStatus::EXPIRED,
                CouponStatus::DEACTIVATED->getLabel() => CouponStatus::DEACTIVATED,
            ])
            ->setHelp('券的当前状态')
            ->renderAsBadges([
                CouponStatus::SENDED->value => CouponStatus::SENDED->getBadge(),
                CouponStatus::USED->value => CouponStatus::USED->getBadge(),
                CouponStatus::EXPIRED->value => CouponStatus::EXPIRED->getBadge(),
                CouponStatus::DEACTIVATED->value => CouponStatus::DEACTIVATED->getBadge(),
            ])
        ;

        yield DateTimeField::new('expiryTime', '过期时间')
            ->setHelp('券的过期时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setColumns(6)
        ;

        yield DateTimeField::new('usedTime', '使用时间')
            ->setHelp('券的实际使用时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setColumns(6)
            ->hideOnForm()
        ;

        yield TextField::new('transactionId', '交易单号')
            ->setHelp('支付交易单号')
            ->setColumns(6)
            ->hideOnIndex()
            ->hideOnForm()
        ;

        yield TextField::new('useRequestNo', '核销请求号')
            ->setHelp('券核销的请求号')
            ->setColumns(6)
            ->hideOnIndex()
            ->hideOnForm()
        ;

        yield TextareaField::new('useInfo', '使用信息')
            ->setHelp('JSON格式：券的使用详细信息')
            ->setNumOfRows(4)
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }
}
