# 微信支付商家券模块

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-pay-busifavor-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-pay-busifavor-bundle)
[![PHP Version](https://img.shields.io/badge/php-^8.1-blue.svg?style=flat-square)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-pay-busifavor-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-pay-busifavor-bundle)
[![Coverage Status](https://img.shields.io/badge/coverage-100%25-brightgreen?style=flat-square)](https://github.com/tourze/wechat-pay-busifavor-bundle)

微信支付商家券是微信支付提供的一种营销工具，商户可以通过创建商家券来吸引和留住客户。本模块提供了完整的微信支付商家券管理功能。

## 功能特性

- 创建和管理商家券批次
- 查询商家券批次详情和状态
- 查询用户券列表（支持筛选）
- 查询用户单张券详情
- 核销商家券
- 与微信支付同步商家券状态
- 命令行管理工具
- 基于 Doctrine ORM 的实体数据持久化
- 类型安全的枚举状态支持
- 全面的 API 客户端和错误处理

## 系统要求

- PHP 8.1 或更高版本
- Symfony 7.3 或更高版本
- Doctrine ORM 3.0 或更高版本

## 安装

```bash
composer require tourze/wechat-pay-busifavor-bundle
```

Bundle 会通过 Symfony Flex 自动注册到 `config/bundles.php`，或者您也可以手动添加：

```php
return [
    // ...
    WechatPayBusifavorBundle\WechatPayBusifavorBundle::class => ['all' => true],
];
```

## 配置

此 Bundle 需要以下配置：
- `HttpClientBundle` - 提供 HTTP 客户端服务
- `WechatPayBundle` - 提供微信支付 API 凭证和配置

## 使用方法

### 依赖注入

```php
use WechatPayBusifavorBundle\Service\BusifavorService;

class YourController
{
    public function __construct(
        private readonly BusifavorService $busifavorService,
    ) {}
}
```

### 创建商家券批次

```php
$data = [
    'stock_name' => '示例券批次',
    'comment' => '描述信息',
    'belong_merchant' => '商户号',
    'available_begin_time' => '2023-01-01T00:00:00+08:00',
    'available_end_time' => '2023-12-31T23:59:59+08:00',
    'stock_use_rule' => [
        'max_coupons' => 100,
        'max_coupons_per_user' => 10
    ],
    'coupon_use_rule' => [
        'fixed_normal_coupon' => [
            'discount_amount' => 100,
            'transaction_minimum' => 100
        ]
    ]
];

$result = $busifavorService->createStock($data);
```

### 查询商家券批次详情

```php
$stock = $busifavorService->getStock($stockId);
```

### 查询用户券列表

```php
$coupons = $busifavorService->getUserCoupons($openid, $appid, $stockId);
```

### 核销商家券

```php
$result = $busifavorService->useCoupon($couponCode, $stockId, $appid, $openid, $useRequestNo);
```

## 命令行工具

### 同步商家券批次状态

```bash
php bin/console wechat-pay:busifavor:sync-stock [stock-id]
```

### 查询用户商家券列表

```bash
php bin/console wechat-pay:busifavor:list-user-coupons openid [--appid=] [--stock-id=] [--status=] [--limit=10] [--offset=0]
```

## 实体类

### Stock 实体

表示商家券批次，包含以下主要属性：

- `stockId`: 券批次ID
- `stockName`: 券批次名称
- `description`: 描述信息
- `status`: 批次状态
- `belongMerchant`: 归属商户号

### Coupon 实体

表示商家券，包含以下主要属性：

- `couponCode`: 券码
- `stockId`: 批次ID
- `openid`: 用户openid
- `status`: 券状态
- `useTime`: 使用时间

## 枚举类

### StockStatus 枚举

商家券批次状态：

- `UNAUDIT`: 未激活
- `CHECKING`: 审核中
- `AUDIT_REJECT`: 审核失败
- `AUDIT_SUCCESS`: 通过审核
- `ONGOING`: 进行中
- `PAUSED`: 已暂停
- `STOPPED`: 已停止
- `EXPIRED`: 已作废

### CouponStatus 枚举

商家券状态：

- `SENDED`: 可用
- `USED`: 已核销
- `EXPIRED`: 已过期
- `DEACTIVATED`: 已失效

## License

本项目采用 MIT 许可证 - 详情请参阅 [LICENSE](LICENSE) 文件。

## 参考文档

- [微信支付商家券API文档](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter9_2_1.shtml)
