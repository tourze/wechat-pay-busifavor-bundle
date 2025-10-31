<?php

namespace WechatPayBusifavorBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 微信支付商家券状态枚举
 *
 * 参考文档: https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter9_2_2.shtml
 */
enum CouponStatus: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use SelectTrait;
    use ItemTrait;

    case SENDED = 'SENDED';
    case USED = 'USED';
    case EXPIRED = 'EXPIRED';
    case DEACTIVATED = 'DEACTIVATED';

    public function getLabel(): string
    {
        return match ($this) {
            self::SENDED => '可用',
            self::USED => '已核销',
            self::EXPIRED => '已过期',
            self::DEACTIVATED => '已失效',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::SENDED => self::SUCCESS,
            self::USED => self::PRIMARY,
            self::EXPIRED => self::WARNING,
            self::DEACTIVATED => self::DANGER,
        };
    }
}
