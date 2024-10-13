<?php
namespace SajidPatel\PriceDropNotification\Api\Data;

interface NotificationInterface
{
    const NOTIFICATION_ID = 'notification_id';
    const PRODUCT_SKU = 'product_sku';
    const EMAIL = 'email';
    const THRESHOLD = 'threshold';
    const CREATED_AT = 'created_at';
    const CUSTOMER_ID = 'customer_id';

    /**
     * Get notification id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set notification id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get product SKU
     *
     * @return string
     */
    public function getProductSku();

    /**
     * Set product SKU
     *
     * @param string $sku
     * @return $this
     */
    public function setProductSku($sku);

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get threshold
     *
     * @return float|null
     */
    public function getThreshold();

    /**
     * Set threshold
     *
     * @param float|null $threshold
     * @return $this
     */
    public function setThreshold($threshold);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int|null $customerId
     * @return $this
     */
    public function setCustomerId($customerId);
}
