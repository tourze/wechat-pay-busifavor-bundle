<?php

namespace WechatPayBusifavorBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class GetStockRequest implements RequestInterface
{
    public function __construct(
        private readonly string $stockId,
    ) {
    }

    public function getRequestPath(): string
    {
        return 'v3/marketing/busifavor/stocks/' . $this->stockId;
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getRequestOptions(): ?array
    {
        return [];
    }
}
