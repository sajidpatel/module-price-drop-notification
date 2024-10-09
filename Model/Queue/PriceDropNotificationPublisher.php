<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Model\Queue;

use Magento\Framework\MessageQueue\PublisherInterface;
use SajidPatel\PriceDropNotification\Api\Data\PriceDropNotificationMessageInterface;
use Psr\Log\LoggerInterface;

/**
 * Publishes price drop notification messages to the queue
 */
class PriceDropNotificationPublisher
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PublisherInterface $publisher
     * @param LoggerInterface $logger
     */
    public function __construct(
        PublisherInterface $publisher,
        LoggerInterface $logger
    ) {
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * Publish a price drop notification message to the queue
     *
     * @param PriceDropNotificationMessageInterface $message
     * @return void
     */
    public function publish(PriceDropNotificationMessageInterface $message): void
    {
        try {
            $this->publisher->publish('sajidpatel.price_drop_notification', $message);
            $this->logger->info('Price drop notification published for product: ' . $message->getProductId());
        } catch (\Exception $e) {
            $this->logger->error('Failed to publish price drop notification: ' . $e->getMessage(), [
                'product_id' => $message->getProductId(),
                'customer_email' => $message->getCustomerEmail(),
                'exception' => $e
            ]);
        }
    }
}
