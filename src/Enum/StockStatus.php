<?php

namespace WechatPayBusifavorBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 微信支付商家券批次状态枚举
 *
 * 参考文档: https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter9_2_2.shtml
 */
enum StockStatus: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use SelectTrait;
    use ItemTrait;

    case UNAUDIT = 'UNAUDIT';
    case CHECKING = 'CHECKING';
    case AUDIT_REJECT = 'AUDIT_REJECT';
    case AUDIT_SUCCESS = 'AUDIT_SUCCESS';
    case ONGOING = 'ONGOING';
    case PAUSED = 'PAUSED';
    case STOPPED = 'STOPPED';
    case EXPIRED = 'EXPIRED';

    public function getLabel(): string
    {
        return match ($this) {
            self::UNAUDIT => '未激活',
            self::CHECKING => '审核中',
            self::AUDIT_REJECT => '审核失败',
            self::AUDIT_SUCCESS => '通过审核',
            self::ONGOING => '进行中',
            self::PAUSED => '已暂停',
            self::STOPPED => '已停止',
            self::EXPIRED => '已作废',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::UNAUDIT => self::SECONDARY,
            self::CHECKING => self::INFO,
            self::AUDIT_REJECT => self::DANGER,
            self::AUDIT_SUCCESS => self::SUCCESS,
            self::ONGOING => self::PRIMARY,
            self::PAUSED => self::WARNING,
            self::STOPPED => self::DARK,
            self::EXPIRED => self::LIGHT,
        };
    }
}
