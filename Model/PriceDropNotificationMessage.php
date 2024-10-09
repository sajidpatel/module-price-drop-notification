<?php
namespace SajidPatel\PriceDropNotification\Model;

use SajidPatel\PriceDropNotification\Api\Data\PriceDropNotificationMessageInterface;

class PriceDropNotificationMessage implements PriceDropNotificationMessageInterface
{
    private $productId;
    private $customerEmail;
    private $oldPrice;
    private $newPrice;

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;
        return $this;
    }

    public function getNewPrice()
    {
        return $this->newPrice;
    }

    public function setNewPrice($newPrice)
    {
        $this->newPrice = $newPrice;
        return $this;
    }
}
