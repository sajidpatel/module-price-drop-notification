<?php
namespace SajidPatel\PriceDropNotification\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use SajidPatel\PriceDropNotification\Model\Queue\PriceDropNotificationPublisher;
use SajidPatel\PriceDropNotification\Model\PriceDropNotificationMessage;

class PriceChange implements ObserverInterface
{
    protected $notificationCollectionFactory;
    protected $scopeConfig;
    protected $publisher;

    public function __construct(
        CollectionFactory $notificationCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        PriceDropNotificationPublisher $publisher
    ) {
        $this->notificationCollectionFactory = $notificationCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->publisher = $publisher;
    }

    public function execute(Observer $observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $product = $observer->getEvent()->getProduct();
        $oldPrice = $product->getOrigData('price');
        $newPrice = $product->getData('price');

        if ($newPrice < $oldPrice) {
            $notifications = $this->notificationCollectionFactory->create()
                ->addFieldToFilter('product_id', $product->getId());

            foreach ($notifications as $notification) {
                $message = new PriceDropNotificationMessage();
                $message->setProductId($product->getId())
                    ->setCustomerEmail($notification->getEmail())
                    ->setOldPrice($oldPrice)
                    ->setNewPrice($newPrice);

                $this->publisher->publish($message);
            }
        }
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $product = $observer->getEvent()->getProduct();

            if (!$this->isEnabledForProduct($product)) {
                return;
            }

            $oldPrice = $product->getOrigData('price');
            $newPrice = $product->getData('price');

            if ($newPrice < $oldPrice) {
                $this->processNotifications($product, $oldPrice, $newPrice);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error processing price change: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Check if the price drop notification feature is enabled for a specific product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    private function isEnabledForProduct($product): bool
    {
        return (bool)$product->getData('enable_price_drop_notification');
    }

    private function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'price_drop_notification/general/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }
}
