<?php

namespace SajidPatel\PriceDropNotification\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use SajidPatel\PriceDropNotification\Api\Data\NotificationInterface;

interface NotificationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get notifications list.
     *
     * @return NotificationInterface[]
     */
    public function getItems();

    /**
     * Set notifications list.
     *
     * @param NotificationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
