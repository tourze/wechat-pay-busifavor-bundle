<?php

namespace WechatPayBusifavorBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class GetCouponRequest implements RequestInterface
{
    private string $couponCode;

    private string $openid;

    private string $appid;

    public function __construct(string $couponCode, string $openid, string $appid)
    {
        $this->couponCode = $couponCode;
        $this->openid = $openid;
        $this->appid = $appid;
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
