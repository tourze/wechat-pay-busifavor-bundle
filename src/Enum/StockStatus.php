<?php

namespace WechatPayBusifavorBundle\Enum;

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
enum StockStatus: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

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
