<?php

namespace SajidPatel\PriceDropNotification\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use SajidPatel\PriceDropNotification\Api\Data\NotificationInterface;
use SajidPatel\PriceDropNotification\Api\Data\NotificationSearchResultsInterface;

interface NotificationRepositoryInterface
{
    /**
     * Save Notification.
     *
     * @param NotificationInterface $notification
     * @return NotificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(NotificationInterface $notification);

    /**
     * Retrieve notification.
     *
     * @param int $notificationId
     * @return NotificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($notificationId);

    /**
     * Retrieve notifications matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return NotificationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete notification.
     *
     * @param NotificationInterface $notification
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(NotificationInterface $notification);

    /**
     * Delete Notification by ID.
     *
     * @param int $notificationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($notificationId);

    /**
     * Get notifications by customer ID.
     *
     * @param int $customerId
     * @return NotificationInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCustomerId($customerId);

    /**
     * Subscribe to price drop notification.
     *
     * @param string $productSku
     * @param string $email
     * @param float|null $threshold
     * @return NotificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function subscribe($productSku, $email, $threshold = null);

    /**
     * Unsubscribe from price drop notification.
     *
     * @param string $productSku
     * @param string $email
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function unsubscribe($productSku, $email);
}
