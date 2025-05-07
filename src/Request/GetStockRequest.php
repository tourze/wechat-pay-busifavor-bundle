<?php

namespace WechatPayBusifavorBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class GetStockRequest implements RequestInterface
{
    private string $stockId;

    public function __construct(string $stockId)
    {
        $this->stockId = $stockId;
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
