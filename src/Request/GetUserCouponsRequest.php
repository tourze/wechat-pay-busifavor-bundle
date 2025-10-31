<?php

namespace WechatPayBusifavorBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class GetUserCouponsRequest implements RequestInterface
{
    public function __construct(
        private readonly string $openid,
        private readonly string $appid,
        private readonly ?string $stockId = null,
        private readonly ?string $status = null,
        private readonly ?int $offset = null,
        private readonly ?int $limit = null,
    ) {
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
