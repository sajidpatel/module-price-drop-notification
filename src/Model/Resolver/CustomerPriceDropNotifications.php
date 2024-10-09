<?php
declare(strict_types=1);

namespace MumzWorld\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use MumzWorld\PriceDropNotification\Model\ResourceModel\Notification\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CustomerPriceDropNotifications implements ResolverInterface
{
    private $notificationCollectionFactory;
    private $customerSession;
    private $productRepository;

    public function __construct(
        CollectionFactory $notificationCollectionFactory,
        Session $customerSession,
        ProductRepositoryInterface $productRepository
    ) {
        $this->notificationCollectionFactory = $notificationCollectionFactory;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->customerSession->isLoggedIn()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $collection = $this->notificationCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $this->customerSession->getCustomerId());

        $notifications = [];
        foreach ($collection as $notification) {
            $product = $this->productRepository->getById($notification->getProductId());
            $notifications[] = [
                'id' => $notification->getId(),
                'product' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'sku' => $product->getSku(),
                    'price_range' => $this->getPriceRange($product),
                    'price_drop_notification_enabled' => (bool)$product->getData('enable_price_drop_notification')
                ],
                'created_at' => $notification->getCreatedAt()
            ];
        }

        return $notifications;
    }

    private function getPriceRange($product)
    {
        $price = $product->getPriceInfo()->getPrice('final_price');
        return [
            'minimum_price' => [
                'regular_price' => [
                    'value' => $price->getAmount()->getValue(),
                    'currency' => $price->getAmount()->getCurrency()->getCurrencyCode()
                ],
                'final_price' => [
                    'value' => $price->getAmount()->getValue(),
                    'currency' => $price->getAmount()->getCurrency()->getCurrencyCode()
                ]
            ],
            'maximum_price' => [
                'regular_price' => [
                    'value' => $price->getAmount()->getValue(),
                    'currency' => $price->getAmount()->getCurrency()->getCurrencyCode()
                ],
                'final_price' => [
                    'value' => $price->getAmount()->getValue(),
                    'currency' => $price->getAmount()->getCurrency()->getCurrencyCode()
                ]
            ]
        ];
    }
}
