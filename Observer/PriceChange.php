<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use SajidPatel\PriceDropNotification\Model\Queue\PriceDropNotificationPublisher;
use SajidPatel\PriceDropNotification\Model\PriceDropNotificationMessage;
use Magento\Catalog\Api\Data\ProductInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;

class PriceChange implements ObserverInterface
{
    private const XML_PATH_ENABLED = 'price_drop_notification/general/enabled';
    private const XML_PATH_MINIMUM_PRICE_DROP_PERCENTAGE = 'price_drop_notification/general/minimum_price_drop_percentage';

    private $notificationCollectionFactory;
    private $scopeConfig;
    private $publisher;
    private $logger;

    public function __construct(
        CollectionFactory $notificationCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        PriceDropNotificationPublisher $publisher,
        LoggerInterface $logger
    ) {
        $this->notificationCollectionFactory = $notificationCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $product = $observer->getEvent()->getProduct();
            if (!$product instanceof ProductInterface) {
                throw new LocalizedException(__('Invalid product data in observer.'));
            }

            $oldPrice = (float)$product->getOrigData('price');
            $newPrice = (float)$product->getData('price');

            if ($this->isPriceDropped($oldPrice, $newPrice)) {
                $this->processNotifications($product, $oldPrice, $newPrice);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in PriceChange observer: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    private function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    private function isPriceDropped(float $oldPrice, float $newPrice): bool
    {
        return $newPrice < $oldPrice;
    }

    private function calculatePriceDropPercentage(float $oldPrice, float $newPrice): float
    {
        return (($oldPrice - $newPrice) / $oldPrice) * 100;
    }

    private function processNotifications(ProductInterface $product, float $oldPrice, float $newPrice): void
    {
        $priceDropPercentage = $this->calculatePriceDropPercentage($oldPrice, $newPrice);
        $notifications = $this->notificationCollectionFactory->create()
            ->addFieldToFilter('product_sku', $product->getSku())
            ->addFieldToFilter('threshold', ['gteq' => $priceDropPercentage]);

        foreach ($notifications as $notification) {
            try {
                $message = $this->createNotificationMessage($product, $notification, $oldPrice, $newPrice, $priceDropPercentage);
                $this->publisher->publish($message);
            } catch (\Exception $e) {
                $this->logger->error(
                    'Error publishing price drop notification: ' . $e->getMessage(),
                    ['exception' => $e, 'notification_id' => $notification->getId()]
                );
            }
        }
    }

    private function createNotificationMessage(
        ProductInterface $product,
        $notification,
        float $oldPrice,
        float $newPrice,
        float $priceDropPercentage
    ): PriceDropNotificationMessage {
        $message = new PriceDropNotificationMessage();
        $message->setProductId($product->getId())
            ->setCustomerEmail($notification->getEmail())
            ->setOldPrice($oldPrice)
            ->setNewPrice($newPrice)
            ->setPriceDropPercentage($priceDropPercentage);

        return $message;
    }
}
