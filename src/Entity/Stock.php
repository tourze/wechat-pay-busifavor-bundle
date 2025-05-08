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
use WechatPayBusifavorBundle\Enum\StockStatus;

#[ORM\Entity(repositoryClass: \WechatPayBusifavorBundle\Repository\StockRepository::class)]
#[ORM\Table(name: 'ims_wechat_pay_busifavor_stock', options: ['comment' => '微信支付商家券批次表'])]
class Stock implements PlainArrayInterface, AdminArrayInterface
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

    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '商家券批次ID'])]
    #[IndexColumn]
    private string $stockId;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '商家券批次名称'])]
    private string $stockName;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '批次描述'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON, options: ['comment' => '可用开始时间'])]
    private array $availableBeginTime = [];

    #[ORM\Column(type: Types::JSON, options: ['comment' => '可用结束时间'])]
    private array $availableEndTime = [];

    #[ORM\Column(type: Types::JSON, options: ['comment' => '批次使用规则'])]
    private array $stockUseRule = [];

    #[ORM\Column(type: Types::JSON, options: ['comment' => '券使用规则'])]
    private array $couponUseRule = [];

    #[ORM\Column(type: Types::JSON, options: ['comment' => '自定义入口'])]
    private array $customEntrance = [];

    #[ORM\Column(type: Types::JSON, options: ['comment' => '展示样式信息'])]
    private array $displayPatternInfo = [];

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '事件通知配置'])]
    private ?array $notifyConfig = null;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '批次状态'], enumType: StockStatus::class)]
    #[IndexColumn]
    private StockStatus $status = StockStatus::UNAUDIT;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '最大发放数量'])]
    private int $maxCoupons;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '每个用户最大可领取数量'])]
    private int $maxCouponsPerUser;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '最大发放金额'])]
    private int $maxAmount;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '单日最大发放金额'])]
    private int $maxAmountByDay;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '剩余可用金额'])]
    private int $remainAmount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '已发放券数量'])]
    private int $distributedCoupons = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否无限制'])]
    private bool $noLimit = false;

    public function getStockId(): string
    {
        return $this->stockId;
    }

    public function setStockId(string $stockId): self
    {
        $this->stockId = $stockId;

        return $this;
    }

    public function getStockName(): string
    {
        return $this->stockName;
    }

    public function setStockName(string $stockName): self
    {
        $this->stockName = $stockName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAvailableBeginTime(): array
    {
        return $this->availableBeginTime;
    }

    public function setAvailableBeginTime(array $availableBeginTime): self
    {
        $this->availableBeginTime = $availableBeginTime;

        return $this;
    }

    public function getAvailableEndTime(): array
    {
        return $this->availableEndTime;
    }

    public function setAvailableEndTime(array $availableEndTime): self
    {
        $this->availableEndTime = $availableEndTime;

        return $this;
    }

    public function getStockUseRule(): array
    {
        return $this->stockUseRule;
    }

    public function setStockUseRule(array $stockUseRule): self
    {
        $this->stockUseRule = $stockUseRule;

        return $this;
    }

    public function getCouponUseRule(): array
    {
        return $this->couponUseRule;
    }

    public function setCouponUseRule(array $couponUseRule): self
    {
        $this->couponUseRule = $couponUseRule;

        return $this;
    }

    public function getCustomEntrance(): array
    {
        return $this->customEntrance;
    }

    public function setCustomEntrance(array $customEntrance): self
    {
        $this->customEntrance = $customEntrance;

        return $this;
    }

    public function getDisplayPatternInfo(): array
    {
        return $this->displayPatternInfo;
    }

    public function setDisplayPatternInfo(array $displayPatternInfo): self
    {
        $this->displayPatternInfo = $displayPatternInfo;

        return $this;
    }

    public function getNotifyConfig(): ?array
    {
        return $this->notifyConfig;
    }

    public function setNotifyConfig(?array $notifyConfig): self
    {
        $this->notifyConfig = $notifyConfig;

        return $this;
    }

    public function getStatus(): StockStatus
    {
        return $this->status;
    }

    public function setStatus(StockStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getMaxCoupons(): int
    {
        return $this->maxCoupons;
    }

    public function setMaxCoupons(int $maxCoupons): self
    {
        $this->maxCoupons = $maxCoupons;

        return $this;
    }

    public function getMaxCouponsPerUser(): int
    {
        return $this->maxCouponsPerUser;
    }

    public function setMaxCouponsPerUser(int $maxCouponsPerUser): self
    {
        $this->maxCouponsPerUser = $maxCouponsPerUser;

        return $this;
    }

    public function getMaxAmount(): int
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(int $maxAmount): self
    {
        $this->maxAmount = $maxAmount;

        return $this;
    }

    public function getMaxAmountByDay(): int
    {
        return $this->maxAmountByDay;
    }

    public function setMaxAmountByDay(int $maxAmountByDay): self
    {
        $this->maxAmountByDay = $maxAmountByDay;

        return $this;
    }

    public function getRemainAmount(): int
    {
        return $this->remainAmount;
    }

    public function setRemainAmount(int $remainAmount): self
    {
        $this->remainAmount = $remainAmount;

        return $this;
    }

    public function getDistributedCoupons(): int
    {
        return $this->distributedCoupons;
    }

    public function setDistributedCoupons(int $distributedCoupons): self
    {
        $this->distributedCoupons = $distributedCoupons;

        return $this;
    }

    public function isNoLimit(): bool
    {
        return $this->noLimit;
    }

    public function setNoLimit(bool $noLimit): self
    {
        $this->noLimit = $noLimit;

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

    public function toAdminArray(): array
    {
        return $this->retrieveAdminArray();
    }

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
}
