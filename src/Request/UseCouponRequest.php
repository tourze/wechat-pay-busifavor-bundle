<?php

namespace WechatPayBusifavorBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class UseCouponRequest implements RequestInterface
{
    public function __construct(
        /** @var array<string, mixed> $requestData */
        private readonly array $requestData,
    ) {
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
