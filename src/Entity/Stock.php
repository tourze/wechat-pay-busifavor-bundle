<?php

namespace WechatPayBusifavorBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatPayBusifavorBundle\Enum\StockStatus;
use WechatPayBusifavorBundle\Repository\StockRepository;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: StockRepository::class)]
#[ORM\Table(name: 'ims_wechat_pay_busifavor_stock', options: ['comment' => '微信支付商家券批次表'])]
class Stock implements PlainArrayInterface, AdminArrayInterface, \Stringable
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

    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '商家券批次ID'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $stockId;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '商家券批次名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $stockName;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '批次描述'])]
    #[Assert\Length(max: 128)]
    private ?string $description = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '可用开始时间'])]
    #[Assert\Type(type: 'array')]
    private array $availableBeginTime = [];

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '可用结束时间'])]
    #[Assert\Type(type: 'array')]
    private array $availableEndTime = [];

    /**
     * @var array{max_coupons?: int, max_coupons_per_user?: int, max_amount?: int, max_amount_by_day?: int, prevent_api_abuse?: bool, ...}
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '批次使用规则'])]
    #[Assert\Type(type: 'array')]
    private array $stockUseRule = [
        'max_coupons_per_user' => 0,
        'max_amount' => 0,
        'prevent_api_abuse' => false,
    ];

    /**
     * @var array{available_merchants?: array<string>, use_limit?: bool, coupon_background?: string, normal_coupon_information?: array<string, mixed>, discount_amount?: int, ...}
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '券使用规则'])]
    #[Assert\Type(type: 'array')]
    private array $couponUseRule = [
        'available_merchants' => [],
        'use_limit' => false,
        'coupon_background' => '',
    ];

    /**
     * @var array{mini_programs_info?: array<string, mixed>|null, ...}
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '自定义入口'])]
    #[Assert\Type(type: 'array')]
    private array $customEntrance = [
        'mini_programs_info' => null,
    ];

    /**
     * @var array{description?: string, logo_url?: string, background_color?: string, ...}
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '展示样式信息'])]
    #[Assert\Type(type: 'array')]
    private array $displayPatternInfo = [
        'description' => '',
        'logo_url' => '',
        'background_color' => '',
    ];

    /**
     * @var array{notify_url?: string}|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '事件通知配置'])]
    #[Assert\Type(type: 'array')]
    private ?array $notifyConfig = null;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '批次状态'], enumType: StockStatus::class)]
    #[IndexColumn]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [StockStatus::class, 'cases'])]
    private StockStatus $status = StockStatus::UNAUDIT;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '最大发放数量'])]
    #[Assert\PositiveOrZero]
    private int $maxCoupons;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '每个用户最大可领取数量'])]
    #[Assert\PositiveOrZero]
    private int $maxCouponsPerUser;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '最大发放金额'])]
    #[Assert\PositiveOrZero]
    private int $maxAmount;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '单日最大发放金额'])]
    #[Assert\PositiveOrZero]
    private int $maxAmountByDay;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '剩余可用金额'])]
    #[Assert\PositiveOrZero]
    private int $remainAmount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '已发放券数量'])]
    #[Assert\PositiveOrZero]
    private int $distributedCoupons = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否无限制'])]
    #[Assert\Type(type: 'bool')]
    private bool $noLimit = false;

    public function getStockId(): string
    {
        return $this->stockId;
    }

    public function setStockId(string $stockId): void
    {
        $this->stockId = $stockId;
    }

    public function getStockName(): string
    {
        return $this->stockName;
    }

    public function setStockName(string $stockName): void
    {
        $this->stockName = $stockName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /** @return array<string, mixed> */
    public function getAvailableBeginTime(): array
    {
        return $this->availableBeginTime;
    }

    /** @param array<string, mixed> $availableBeginTime */
    public function setAvailableBeginTime(array $availableBeginTime): void
    {
        $this->availableBeginTime = $availableBeginTime;
    }

    /** @return array<string, mixed> */
    public function getAvailableEndTime(): array
    {
        return $this->availableEndTime;
    }

    /** @param array<string, mixed> $availableEndTime */
    public function setAvailableEndTime(array $availableEndTime): void
    {
        $this->availableEndTime = $availableEndTime;
    }

    /** @return array<string, mixed> */
    public function getStockUseRule(): array
    {
        return $this->stockUseRule;
    }

    /** @param array{max_coupons?: int, max_coupons_per_user?: int, max_amount?: int, max_amount_by_day?: int, prevent_api_abuse?: bool, ...} $stockUseRule */
    public function setStockUseRule(array $stockUseRule): void
    {
        $this->stockUseRule = $stockUseRule;
    }

    /** @return array<string, mixed> */
    public function getCouponUseRule(): array
    {
        return $this->couponUseRule;
    }

    /** @param array{available_merchants?: array<string>, use_limit?: bool, coupon_background?: string, normal_coupon_information?: array<string, mixed>, discount_amount?: int, ...} $couponUseRule */
    public function setCouponUseRule(array $couponUseRule): void
    {
        $this->couponUseRule = $couponUseRule;
    }

    /** @return array<string, mixed> */
    public function getCustomEntrance(): array
    {
        return $this->customEntrance;
    }

    /** @param array{mini_programs_info?: array<string, mixed>|null, ...} $customEntrance */
    public function setCustomEntrance(array $customEntrance): void
    {
        $this->customEntrance = $customEntrance;
    }

    /** @return array<string, mixed> */
    public function getDisplayPatternInfo(): array
    {
        return $this->displayPatternInfo;
    }

    /** @param array{description?: string, logo_url?: string, background_color?: string, ...} $displayPatternInfo */
    public function setDisplayPatternInfo(array $displayPatternInfo): void
    {
        $this->displayPatternInfo = $displayPatternInfo;
    }

    /** @return array<string, mixed>|null */
    public function getNotifyConfig(): ?array
    {
        return $this->notifyConfig;
    }

    /** @param array{notify_url?: string}|null $notifyConfig */
    public function setNotifyConfig(?array $notifyConfig): void
    {
        $this->notifyConfig = $notifyConfig;
    }

    public function getStatus(): StockStatus
    {
        return $this->status;
    }

    public function setStatus(StockStatus $status): void
    {
        $this->status = $status;
    }

    public function getMaxCoupons(): int
    {
        return $this->maxCoupons;
    }

    public function setMaxCoupons(int $maxCoupons): void
    {
        $this->maxCoupons = $maxCoupons;
    }

    public function getMaxCouponsPerUser(): int
    {
        return $this->maxCouponsPerUser;
    }

    public function setMaxCouponsPerUser(int $maxCouponsPerUser): void
    {
        $this->maxCouponsPerUser = $maxCouponsPerUser;
    }

    public function getMaxAmount(): int
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(int $maxAmount): void
    {
        $this->maxAmount = $maxAmount;
    }

    public function getMaxAmountByDay(): int
    {
        return $this->maxAmountByDay;
    }

    public function setMaxAmountByDay(int $maxAmountByDay): void
    {
        $this->maxAmountByDay = $maxAmountByDay;
    }

    public function getRemainAmount(): int
    {
        return $this->remainAmount;
    }

    public function setRemainAmount(int $remainAmount): void
    {
        $this->remainAmount = $remainAmount;
    }

    public function getDistributedCoupons(): int
    {
        return $this->distributedCoupons;
    }

    public function setDistributedCoupons(int $distributedCoupons): void
    {
        $this->distributedCoupons = $distributedCoupons;
    }

    public function isNoLimit(): bool
    {
        return $this->noLimit;
    }

    public function setNoLimit(bool $noLimit): void
    {
        $this->noLimit = $noLimit;
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
            'stockId' => $this->stockId,
            'stockName' => $this->getStockName(),
            'description' => $this->getDescription(),
            'status' => $this->getStatus(),
            'maxCoupons' => $this->getMaxCoupons(),
            'maxCouponsPerUser' => $this->getMaxCouponsPerUser(),
            'maxAmount' => $this->maxAmount,
            'maxAmountByDay' => $this->maxAmountByDay,
            'remainAmount' => $this->getRemainAmount(),
            'distributedCoupons' => $this->getDistributedCoupons(),
            'noLimit' => $this->noLimit,
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
            'availableBeginTime' => $this->availableBeginTime,
            'availableEndTime' => $this->availableEndTime,
            'stockUseRule' => $this->stockUseRule,
            'couponUseRule' => $this->couponUseRule,
            'customEntrance' => $this->customEntrance,
            'displayPatternInfo' => $this->displayPatternInfo,
            'notifyConfig' => $this->notifyConfig,
        ];
    }

    public function __toString(): string
    {
        return $this->stockName ?? $this->stockId;
    }
}
