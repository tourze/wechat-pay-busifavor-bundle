# 微信支付商家券模块

微信支付商家券是微信支付提供的一种营销工具，商户可以通过创建商家券来吸引和留住客户。本模块提供了完整的微信支付商家券管理功能。

## 功能特性

- 创建商家券批次
- 查询商家券批次详情
- 查询用户券列表
- 查询用户单张券详情
- 核销商家券
- 同步商家券状态
- 支持命令行管理

## 依赖关系

- HttpClientBundle
- WechatPayBundle

## API接口

### 创建商家券批次

```
POST /api/wechat-pay/busifavor/stocks
```

请求示例:
```json
{
    "stock_name": "示例券批次",
    "comment": "描述信息",
    "belong_merchant": "商户号",
    "available_begin_time": "2023-01-01T00:00:00+08:00",
    "available_end_time": "2023-12-31T23:59:59+08:00",
    "stock_use_rule": {
        "max_coupons": 100,
        "max_coupons_per_user": 10
    },
    "coupon_use_rule": {
        "fixed_normal_coupon": {
            "discount_amount": 100,
            "transaction_minimum": 100
        }
    }
}
```

### 查询商家券批次详情

```
GET /api/wechat-pay/busifavor/stocks/{stockId}
```

### 查询用户券列表

```
GET /api/wechat-pay/busifavor/users/{openid}/coupons
```

### 核销商家券

```
POST /api/wechat-pay/busifavor/coupons/use
```

请求示例:
```json
{
    "coupon_code": "券码",
    "stock_id": "批次ID",
    "openid": "用户openid"
}
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

## 相关文档

- [微信支付商家券API文档](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter9_2_1.shtml)
