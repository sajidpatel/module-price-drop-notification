<?php

declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;

/**
 * Resolver for customer price drop notifications
 */
class CustomerPriceDropNotifications implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $notificationCollectionFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param CollectionFactory $notificationCollectionFactory
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CollectionFactory $notificationCollectionFactory,
        Session $customerSession,
        ProductRepositoryInterface $productRepository
    ) {
        $this->notificationCollectionFactory = $notificationCollectionFactory;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!$this->customerSession->isLoggedIn()) {
            throw new GraphQlAuthorizationException(
                __('The current customer isn\'t authorized.')
            );
        }

        $collection = $this->notificationCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $this->customerSession->getCustomerId());

        $notifications = [];
        foreach ($collection as $notification) {
            $product = $this->productRepository->getById($notification->getProductId());
            $notifications[] = $this->formatNotification($notification, $product);
        }

        return $notifications;
    }

    /**
     * Format notification data
     *
     * @param \SajidPatel\PriceDropNotification\Model\Notification $notification
     * @param Product $product
     * @return array
     */
    private function formatNotification($notification, Product $product): array
    {
        return [
            'id' => $notification->getId(),
            'product' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'price_range' => $this->getPriceRange($product),
                'price_drop_notification_enabled' =>
                    (bool)$product->getData('enable_price_drop_notification')
            ],
            'created_at' => $notification->getCreatedAt()
        ];
    }

    /**
     * Get price range for product
     *
     * @param Product $product
     * @return array
     */
    private function getPriceRange(Product $product): array
    {
        $price = $product->getPriceInfo()->getPrice('final_price');
        $priceValue = $price->getAmount()->getValue();
        $currencyCode = $price->getAmount()->getCurrency()->getCurrencyCode();

        return [
            'minimum_price' => $this->formatPrice($priceValue, $currencyCode),
            'maximum_price' => $this->formatPrice($priceValue, $currencyCode)
        ];
    }

    /**
     * Format price data
     *
     * @param float $value
     * @param string $currencyCode
     * @return array
     */
    private function formatPrice(float $value, string $currencyCode): array
    {
        return [
            'regular_price' => [
                'value' => $value,
                'currency' => $currencyCode
            ],
            'final_price' => [
                'value' => $value,
                'currency' => $currencyCode
            ]
        ];
    }
}
