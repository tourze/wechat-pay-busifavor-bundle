<?php

namespace WechatPayBusifavorBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use WechatPayBusifavorBundle\Enum\CouponStatus;

#[ORM\Entity(repositoryClass: \WechatPayBusifavorBundle\Repository\CouponRepository::class)]
#[ORM\Table(name: 'ims_wechat_pay_busifavor_coupon', options: ['comment' => '微信支付商家券表'])]
class Coupon implements PlainArrayInterface, AdminArrayInterface
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '商家券券码'])]
    #[IndexColumn]
    private string $couponCode;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '商家券批次ID'])]
    #[IndexColumn]
    private string $stockId;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '用户openid'])]
    #[IndexColumn]
    private ?string $openid = null;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '券状态'], enumType: CouponStatus::class)]
    #[IndexColumn]
    private CouponStatus $status = CouponStatus::SENDED;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '使用时间'])]
    private ?\DateTimeImmutable $usedTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '过期时间'])]
    private ?\DateTimeImmutable $expiryTime = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '支付交易单号'])]
    private ?string $transactionId = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '核销请求号'])]
    private ?string $useRequestNo = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '使用信息'])]
    private ?array $useInfo = null;

    public function getCouponCode(): string
    {
        return $this->couponCode;
    }

    public function setCouponCode(string $couponCode): self
    {
        $this->couponCode = $couponCode;

        return $this;
    }

    public function getStockId(): string
    {
        return $this->stockId;
    }

    public function setStockId(string $stockId): self
    {
        $this->stockId = $stockId;

        return $this;
    }

    public function getOpenid(): ?string
    {
        return $this->openid;
    }

    public function setOpenid(?string $openid): self
    {
        $this->openid = $openid;

        return $this;
    }

    public function getStatus(): CouponStatus
    {
        return $this->status;
    }

    public function setStatus(CouponStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUsedTime(): ?\DateTimeImmutable
    {
        return $this->usedTime;
    }

    public function setUsedTime(?\DateTimeImmutable $usedTime): self
    {
        $this->usedTime = $usedTime;

        return $this;
    }

    public function getExpiryTime(): ?\DateTimeImmutable
    {
        return $this->expiryTime;
    }

    public function setExpiryTime(?\DateTimeImmutable $expiryTime): self
    {
        $this->expiryTime = $expiryTime;

        return $this;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getUseRequestNo(): ?string
    {
        return $this->useRequestNo;
    }

    public function setUseRequestNo(?string $useRequestNo): self
    {
        $this->useRequestNo = $useRequestNo;

        return $this;
    }

    public function getUseInfo(): ?array
    {
        return $this->useInfo;
    }

    public function setUseInfo(?array $useInfo): self
    {
        $this->useInfo = $useInfo;

        return $this;
    }

    public function toPlainArray(): array
    {
        return $this->retrievePlainArray();
    }

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'couponCode' => $this->couponCode,
            'stockId' => $this->stockId,
            'openid' => $this->openid,
            'status' => $this->status,
            'expiryTime' => $this->expiryTime?->format('Y-m-d H:i:s'),
            'usedTime' => $this->usedTime?->format('Y-m-d H:i:s'),
            'transactionId' => $this->transactionId,
            'useRequestNo' => $this->useRequestNo,
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function toAdminArray(): array
    {
        return $this->retrieveAdminArray();
    }

    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray() + [
            'useInfo' => $this->useInfo,
        ];
    }
}
