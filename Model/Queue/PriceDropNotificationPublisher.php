<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Model\Queue;

use Magento\Framework\MessageQueue\PublisherInterface;
use SajidPatel\PriceDropNotification\Api\Data\PriceDropNotificationMessageInterface;
use Psr\Log\LoggerInterface;

class PriceDropNotificationPublisher
{
    protected const TOPIC_NAME = 'sajidpatel.price_drop_notification';

    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
            $this->publisher->publish(self::TOPIC_NAME, $message);
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
