<?php
namespace SajidPatel\PriceDropNotification\Model;

use Magento\Framework\Model\AbstractModel;
use SajidPatel\PriceDropNotification\Api\Data\NotificationInterface;
use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification as ResourceModel;

class Notification extends AbstractModel implements NotificationInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    public function getProductSku()
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    public function setProductSku($sku)
    {
        return $this->setData(self::PRODUCT_SKU, $sku);
    }

    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    public function getThreshold()
    {
        return $this->getData(self::THRESHOLD);
    }

    public function setThreshold($threshold)
    {
        return $this->setData(self::THRESHOLD, $threshold);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
}
