<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Cron;

use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification\CollectionFactory;
use SajidPatel\PriceDropNotification\Model\Notification as NotificationResource;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\ScopeInterface;

class CleanupNotifications
{
    const XML_PATH_CLEANUP_DAYS = 'price_drop_notification/cron_schedule/cleanup_days';
    const XML_PATH_ENABLED = 'price_drop_notification/general/enabled';

    /**
     * @var CollectionFactory
     */
    protected $notificationCollectionFactory;

    /**
     * @var NotificationResource
     */
    protected $notificationResource;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param CollectionFactory $notificationCollectionFactory
     * @param NotificationResource $notificationResource
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CollectionFactory $notificationCollectionFactory,
        NotificationResource $notificationResource,
        DateTime $dateTime,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->notificationCollectionFactory = $notificationCollectionFactory;
        $this->notificationResource = $notificationResource;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Clean up old or fulfilled notifications
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->isEnabled()) {
            $this->logger->info("Price drop notification feature is disabled. Skipping cleanup.");
            return;
        }

        try {
            $collection = $this->notificationCollectionFactory->create();

            $daysToKeep = $this->getDaysToKeep();
            $cutoffDate = $this->dateTime->gmtDate(
                'Y-m-d H:i:s', strtotime("-{$daysToKeep} days")
            );
            $collection->addFieldToFilter('created_at', ['lt' => $cutoffDate]);

            $count = 0;
            foreach ($collection as $notification) {
                $this->notificationResource->delete($notification);
                $count++;
            }

            $this->logger->info(
                "Cleaned up {$count} price drop notifications older than {$daysToKeep} days."
            );
        } catch (\Exception $e) {
            $this->logger->error(
                'Error cleaning up price drop notifications: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }
    }

    /**
     * Check if price_drop_notification feature is enabled
     *
     * @return boolean
     */
    protected function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get the number of days to keep notifications from configuration
     *
     * @return int
     */
    protected function getDaysToKeep(): int
    {
        $configValue = $this->scopeConfig->getValue(self::XML_PATH_CLEANUP_DAYS, ScopeInterface::SCOPE_STORE);
        return (int)$configValue ?: 30; // Default to 30 days if not set
    }
}