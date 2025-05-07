<?php

namespace WechatPayBusifavorBundle\Client;

use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Request\RequestInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BusifavorClient extends ApiClient
{
    private string $baseUrl = 'https://api.mch.weixin.qq.com/';

    protected function getRequestUrl(RequestInterface $request): string
    {
        return $this->baseUrl . $request->getRequestPath();
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return $request->getRequestMethod() ?? 'GET';
    }

    protected function getRequestOptions(RequestInterface $request): array
    {
        return $request->getRequestOptions() ?? [];
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        $content = $response->toArray(false);

        return $content;
    }
}
