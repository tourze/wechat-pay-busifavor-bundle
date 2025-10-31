<?php

namespace WechatPayBusifavorBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class GetCouponRequest implements RequestInterface
{
    public function __construct(
        private readonly string $couponCode,
        private readonly string $openid,
        private readonly string $appid,
    ) {
    }

    public function getRequestPath(): string
    {
        return 'v3/marketing/busifavor/users/' . $this->openid . '/coupons/' . $this->couponCode;
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'appid' => $this->appid,
            ],
        ];
    }
}
