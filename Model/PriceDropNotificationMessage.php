<?php

declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Model;

use SajidPatel\PriceDropNotification\Api\Data\PriceDropNotificationMessageInterface;

/**
 * Price Drop Notification Message Model
 */
class PriceDropNotificationMessage implements PriceDropNotificationMessageInterface
{
    /**
     * @var int|null
     */
    protected $productId;

    /**
     * @var string|null
     */
    protected $customerEmail;

    /**
     * @var float|null
     */
    protected $oldPrice;

    /**
     * @var float|null
     */
    protected $newPrice;

    /**
     * Get product ID
     *
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }

    /**
     * Set product ID
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId(int $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * Get customer email
     *
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    /**
     * Set customer email
     *
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail(string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * Get old price
     *
     * @return float|null
     */
    public function getOldPrice(): ?float
    {
        return $this->oldPrice;
    }

    /**
     * Set old price
     *
     * @param float $oldPrice
     * @return $this
     */
    public function setOldPrice(float $oldPrice): self
    {
        $this->oldPrice = $oldPrice;
        return $this;
    }

    /**
     * Get new price
     *
     * @return float|null
     */
    public function getNewPrice(): ?float
    {
        return $this->newPrice;
    }

    /**
     * Set new price
     *
     * @param float $newPrice
     * @return $this
     */
    public function setNewPrice(float $newPrice): self
    {
        $this->newPrice = $newPrice;
        return $this;
    }
}
