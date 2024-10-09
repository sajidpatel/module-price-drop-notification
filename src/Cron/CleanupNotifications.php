<?php
declare(strict_types=1);

namespace MumzWorld\PriceDropNotification\Cron;

use MumzWorld\PriceDropNotification\Model\ResourceModel\Notification\CollectionFactory;
use MumzWorld\PriceDropNotification\Model\ResourceModel\Notification as NotificationResource;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class CleanupNotifications
{
    /**
     * @var CollectionFactory
     */
    private $notificationCollectionFactory;

    /**
     * @var NotificationResource
     */
    private $notificationResource;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param CollectionFactory $notificationCollectionFactory
     * @param NotificationResource $notificationResource
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
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
            return;
        }

        try {
            $collection = $this->notificationCollectionFactory->create();

            // Remove notifications older than 30 days
            $thirtyDaysAgo = $this->dateTime->gmtDate('Y-m-d H:i:s', strtotime('-30 days'));
            $collection->addFieldToFilter('created_at', ['lt' => $thirtyDaysAgo]);

            $count = 0;
            foreach ($collection as $notification) {
                $this->notificationResource->delete($notification);
                $count++;
            }

            $this->logger->info("Cleaned up {$count} old price drop notifications.");
        } catch (\Exception $e) {
            $this->logger->error('Error cleaning up price drop notifications: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Check if price_drop_notification feature is enable
     *
     * @return boolean
     */
    private function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('price_drop_notification/general/enabled');
    }
}
