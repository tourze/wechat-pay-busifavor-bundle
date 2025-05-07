<?php

namespace WechatPayBusifavorBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class UseCouponRequest implements RequestInterface
{
    private array $requestData;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    public function getRequestPath(): string
    {
        return 'v3/marketing/busifavor/coupons/use';
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => $this->requestData,
        ];
    }
}
