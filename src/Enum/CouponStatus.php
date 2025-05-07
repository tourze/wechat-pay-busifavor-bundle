<?php

namespace WechatPayBusifavorBundle\Enum;

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
enum CouponStatus: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

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

    /**
     * 获取所有状态
     *
     * @return array<string, string> 状态值 => 状态标签
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $status) {
            $options[$status->value] = $status->getLabel();
        }

        return $options;
    }
}
