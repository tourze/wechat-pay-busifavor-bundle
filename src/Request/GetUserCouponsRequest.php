<?php

namespace WechatPayBusifavorBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class GetUserCouponsRequest implements RequestInterface
{
    private string $openid;

    private string $appid;

    private ?string $stockId;

    private ?string $status;

    private ?int $offset;

    private ?int $limit;

    public function __construct(
        string $openid,
        string $appid,
        ?string $stockId = null,
        ?string $status = null,
        ?int $offset = null,
        ?int $limit = null,
    ) {
        $this->openid = $openid;
        $this->appid = $appid;
        $this->stockId = $stockId;
        $this->status = $status;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getRequestPath(): string
    {
        return 'v3/marketing/busifavor/users/' . $this->openid . '/coupons';
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getRequestOptions(): ?array
    {
        $query = [
            'appid' => $this->appid,
        ];

        if (null !== $this->stockId) {
            $query['stock_id'] = $this->stockId;
        }

        if (null !== $this->status) {
            $query['status'] = $this->status;
        }

        if (null !== $this->offset) {
            $query['offset'] = $this->offset;
        }

        if (null !== $this->limit) {
            $query['limit'] = $this->limit;
        }

        return [
            'query' => $query,
        ];
    }
}
