# WeChat Pay Business Coupon Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-pay-busifavor-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-pay-busifavor-bundle)
[![PHP Version](https://img.shields.io/badge/php-^8.1-blue.svg?style=flat-square)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-pay-busifavor-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-pay-busifavor-bundle)
[![Coverage Status](https://img.shields.io/badge/coverage-100%25-brightgreen?style=flat-square)](https://github.com/tourze/wechat-pay-busifavor-bundle)

A comprehensive Symfony bundle for managing WeChat Pay Business Coupons. This module provides complete functionality for creating, managing, and processing business coupons through WeChat Pay's marketing tools.

## Features

- Create and manage business coupon stocks/batches
- Query coupon stock details and status
- Retrieve user coupon lists with filtering
- Get individual coupon details
- Process coupon redemptions
- Synchronize coupon status with WeChat Pay
- Command-line management tools
- Entity-based data persistence with Doctrine ORM
- Type-safe enum support for statuses
- Comprehensive API client with error handling

## Requirements

- PHP 8.1 or higher
- Symfony 7.3 or higher
- Doctrine ORM 3.0 or higher

## Installation

```bash
composer require tourze/wechat-pay-busifavor-bundle
```

The bundle will be automatically registered in your `config/bundles.php` via Symfony Flex, or you can manually add it:

```php
return [
    // ...
    WechatPayBusifavorBundle\WechatPayBusifavorBundle::class => ['all' => true],
];
```

## Configuration

This bundle requires configuration from:
- `HttpClientBundle` - for HTTP client services
- `WechatPayBundle` - for WeChat Pay API credentials and configuration

## Quick Start

### Dependency Injection

```php
use WechatPayBusifavorBundle\Service\BusifavorService;

class YourController
{
    public function __construct(
        private readonly BusifavorService $busifavorService,
    ) {}
}
```

### Create Stock

```php
$data = [
    'stock_name' => 'Example Stock',
    'comment' => 'Description',
    'belong_merchant' => 'merchant_id',
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

## API Reference

### Create Stock

```php
$stock = $busifavorService->createStock($data);
```

### Get Stock Details

```php
$stock = $busifavorService->getStock($stockId);
```

### Get User Coupons

```php
$coupons = $busifavorService->getUserCoupons($openid, $appid, $stockId);
```

### Redeem Coupon

```php
$result = $busifavorService->useCoupon($couponCode, $stockId, $appid, $openid, $useRequestNo);
```

## Console Commands

### Sync Stock Status

```bash
php bin/console wechat-pay:busifavor:sync-stock [stock-id]
```

### List User Coupons

```bash
php bin/console wechat-pay:busifavor:list-user-coupons openid [--appid=] [--stock-id=] [--status=] [--limit=10] [--offset=0]
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## References

- [WeChat Pay Business Coupon API Documentation](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter9_2_1.shtml)
