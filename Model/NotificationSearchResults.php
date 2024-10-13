<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AsynchronousOperations\Model;

use SajidPatel\PriceDropNotification\Api\Data\NotificationSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object with bulk Operation search result.
 */
class NotificationSearchResults extends SearchResults implements NotificationSearchResultsInterface
{
}
