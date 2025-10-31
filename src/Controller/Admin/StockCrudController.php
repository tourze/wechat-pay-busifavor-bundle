<?php

declare(strict_types=1);

namespace WechatPayBusifavorBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatPayBusifavorBundle\Entity\Stock;
use WechatPayBusifavorBundle\Enum\StockStatus;

/**
 * @template-extends AbstractCrudController<Stock>
 */
#[AdminCrud(routePath: '/busifavor/stock', routeName: 'busifavor_stock')]
final class StockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('商家券批次')
            ->setEntityLabelInPlural('商家券批次')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['stockId', 'stockName', 'description'])
            ->setPaginatorPageSize(25)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('stockId', '批次ID'))
            ->add(TextFilter::new('stockName', '批次名称'))
            ->add('status')
            ->add(BooleanFilter::new('noLimit', '无限制'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
            ->setSortable(true)
        ;

        yield TextField::new('stockId', '批次ID')
            ->setRequired(true)
            ->setHelp('微信支付商家券批次ID，用于唯一标识')
            ->setColumns(6)
        ;

        yield TextField::new('stockName', '批次名称')
            ->setRequired(true)
            ->setHelp('商家券批次显示名称')
            ->setColumns(6)
        ;

        yield TextareaField::new('description', '批次描述')
            ->setHelp('批次的详细描述信息')
            ->hideOnIndex()
            ->setNumOfRows(3)
        ;

        yield ChoiceField::new('status', '状态')
            ->setChoices([
                '未激活' => StockStatus::UNAUDIT,
                '审核中' => StockStatus::CHECKING,
                '审核失败' => StockStatus::AUDIT_REJECT,
                '通过审核' => StockStatus::AUDIT_SUCCESS,
                '进行中' => StockStatus::ONGOING,
                '已暂停' => StockStatus::PAUSED,
                '已停止' => StockStatus::STOPPED,
                '已作废' => StockStatus::EXPIRED,
            ])
            ->setHelp('批次当前状态')
            ->renderAsBadges([
                StockStatus::UNAUDIT->value => 'secondary',
                StockStatus::CHECKING->value => 'info',
                StockStatus::AUDIT_REJECT->value => 'danger',
                StockStatus::AUDIT_SUCCESS->value => 'success',
                StockStatus::ONGOING->value => 'primary',
                StockStatus::PAUSED->value => 'warning',
                StockStatus::STOPPED->value => 'dark',
                StockStatus::EXPIRED->value => 'light',
            ])
        ;

        yield IntegerField::new('maxCoupons', '最大发放数量')
            ->setRequired(true)
            ->setHelp('该批次最多可发放的券数量')
            ->setColumns(4)
        ;

        yield IntegerField::new('maxCouponsPerUser', '每用户最大数量')
            ->setRequired(true)
            ->setHelp('每个用户最多可领取的券数量')
            ->setColumns(4)
        ;

        yield IntegerField::new('maxAmount', '最大发放金额')
            ->setRequired(true)
            ->setHelp('该批次最多可发放的总金额（分）')
            ->setColumns(4)
        ;

        yield IntegerField::new('maxAmountByDay', '单日最大金额')
            ->setRequired(true)
            ->setHelp('单日最多可发放的金额（分）')
            ->setColumns(4)
        ;

        yield IntegerField::new('remainAmount', '剩余金额')
            ->setHelp('当前剩余可用金额（分）')
            ->setColumns(4)
            ->hideOnForm()
        ;

        yield IntegerField::new('distributedCoupons', '已发放数量')
            ->setHelp('已发放的券数量')
            ->setColumns(4)
            ->hideOnForm()
        ;

        yield BooleanField::new('noLimit', '无限制')
            ->setHelp('是否不限制发放数量和金额')
            ->renderAsSwitch(false)
        ;

        yield TextareaField::new('availableBeginTime', '可用开始时间')
            ->hideOnIndex()
            ->setHelp('JSON格式：券的可用开始时间配置')
            ->setNumOfRows(4)
            ->onlyOnDetail()
        ;

        yield TextareaField::new('availableEndTime', '可用结束时间')
            ->hideOnIndex()
            ->setHelp('JSON格式：券的可用结束时间配置')
            ->setNumOfRows(4)
            ->onlyOnDetail()
        ;

        yield TextareaField::new('stockUseRule', '批次使用规则')
            ->hideOnIndex()
            ->setHelp('JSON格式：批次的使用规则配置')
            ->setNumOfRows(4)
            ->onlyOnDetail()
        ;

        yield TextareaField::new('couponUseRule', '券使用规则')
            ->hideOnIndex()
            ->setHelp('JSON格式：券的使用规则配置')
            ->setNumOfRows(4)
            ->onlyOnDetail()
        ;

        yield TextareaField::new('customEntrance', '自定义入口')
            ->hideOnIndex()
            ->setHelp('JSON格式：自定义入口配置')
            ->setNumOfRows(4)
            ->onlyOnDetail()
        ;

        yield TextareaField::new('displayPatternInfo', '展示样式信息')
            ->hideOnIndex()
            ->setHelp('JSON格式：券的展示样式配置')
            ->setNumOfRows(4)
            ->onlyOnDetail()
        ;

        yield TextareaField::new('notifyConfig', '通知配置')
            ->hideOnIndex()
            ->setHelp('JSON格式：事件通知配置')
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
