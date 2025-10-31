<?php

namespace WechatPayBusifavorBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatPayBusifavorBundle\Enum\CouponStatus;
use WechatPayBusifavorBundle\Repository\CouponRepository;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ORM\Table(name: 'ims_wechat_pay_busifavor_coupon', options: ['comment' => '微信支付商家券表'])]
class Coupon implements PlainArrayInterface, AdminArrayInterface, \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '商家券券码'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $couponCode;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '商家券批次ID'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $stockId;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '用户openid'])]
    #[IndexColumn]
    #[Assert\Length(max: 64)]
    private ?string $openid = null;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '券状态'], enumType: CouponStatus::class)]
    #[IndexColumn]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [CouponStatus::class, 'cases'])]
    private CouponStatus $status = CouponStatus::SENDED;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '使用时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $usedTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '过期时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $expiryTime = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '支付交易单号'])]
    #[Assert\Length(max: 64)]
    private ?string $transactionId = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '核销请求号'])]
    #[Assert\Length(max: 64)]
    private ?string $useRequestNo = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '使用信息'])]
    #[Assert\Type(type: 'array')]
    private ?array $useInfo = null;

    public function getCouponCode(): string
    {
        return $this->couponCode;
    }

    public function setCouponCode(string $couponCode): void
    {
        $this->couponCode = $couponCode;
    }

    public function getStockId(): string
    {
        return $this->stockId;
    }

    public function setStockId(string $stockId): void
    {
        $this->stockId = $stockId;
    }

    public function getOpenid(): ?string
    {
        return $this->openid;
    }

    public function setOpenid(?string $openid): void
    {
        $this->openid = $openid;
    }

    public function getStatus(): CouponStatus
    {
        return $this->status;
    }

    public function setStatus(CouponStatus $status): void
    {
        $this->status = $status;
    }

    public function getUsedTime(): ?\DateTimeImmutable
    {
        return $this->usedTime;
    }

    public function setUsedTime(?\DateTimeImmutable $usedTime): void
    {
        $this->usedTime = $usedTime;
    }

    public function getExpiryTime(): ?\DateTimeImmutable
    {
        return $this->expiryTime;
    }

    public function setExpiryTime(?\DateTimeImmutable $expiryTime): void
    {
        $this->expiryTime = $expiryTime;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    public function getUseRequestNo(): ?string
    {
        return $this->useRequestNo;
    }

    public function setUseRequestNo(?string $useRequestNo): void
    {
        $this->useRequestNo = $useRequestNo;
    }

    /** @return array<string, mixed>|null */
    public function getUseInfo(): ?array
    {
        return $this->useInfo;
    }

    /** @param array<string, mixed>|null $useInfo */
    public function setUseInfo(?array $useInfo): void
    {
        $this->useInfo = $useInfo;
    }

    /** @return array<string, mixed> */
    public function toPlainArray(): array
    {
        return $this->retrievePlainArray();
    }

    /** @return array<string, mixed> */
    /** @return array<string, mixed> */
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

    /** @return array<string, mixed> */
    public function toAdminArray(): array
    {
        return $this->retrieveAdminArray();
    }

    /** @return array<string, mixed> */
    /** @return array<string, mixed> */
    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray() + [
            'useInfo' => $this->useInfo,
        ];
    }

    public function __toString(): string
    {
        return $this->couponCode . ' - ' . $this->stockId;
    }
}
