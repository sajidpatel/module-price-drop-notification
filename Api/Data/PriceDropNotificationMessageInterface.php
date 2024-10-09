<?php
namespace SajidPatel\PriceDropNotification\Api\Data;

interface PriceDropNotificationMessageInterface
{
    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @return string
     */
    public function getCustomerEmail();

    /**
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * @return float
     */
    public function getOldPrice();

    /**
     * @param float $oldPrice
     * @return $this
     */
    public function setOldPrice($oldPrice);

    /**
     * @return float
     */
    public function getNewPrice();

    /**
     * @param float $newPrice
     * @return $this
     */
    public function setNewPrice($newPrice);
}
