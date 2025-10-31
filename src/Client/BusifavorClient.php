<?php

namespace WechatPayBusifavorBundle\Client;

use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Request\RequestInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;

#[WithMonologChannel(channel: 'wechat_pay_busifavor')]
class BusifavorClient extends ApiClient
{
    private string $baseUrl = 'https://api.mch.weixin.qq.com/';

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly HttpClientInterface $httpClient,
        private readonly LockFactory $lockFactory,
        private readonly CacheInterface $cache,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly AsyncInsertService $asyncInsertService,
    ) {
    }

    protected function getLockFactory(): LockFactory
    {
        return $this->lockFactory;
    }

    protected function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getCache(): CacheInterface
    {
        return $this->cache;
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function getAsyncInsertService(): AsyncInsertService
    {
        return $this->asyncInsertService;
    }

    protected function getRequestUrl(RequestInterface $request): string
    {
        return $this->baseUrl . $request->getRequestPath();
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return $request->getRequestMethod() ?? 'GET';
    }

    protected function getRequestOptions(RequestInterface $request): ?array
    {
        return $request->getRequestOptions();
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        return $response->toArray(false);
    }
}
