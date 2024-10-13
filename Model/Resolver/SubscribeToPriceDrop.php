<?php
namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use SajidPatel\PriceDropNotification\Api\NotificationRepositoryInterface;

class SubscribeToPriceDrop implements ResolverInterface
{
    private $productRepository;
    private $notificationRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->productRepository = $productRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['input']) || !isset($args['input']['product_sku']) || !isset($args['input']['email'])) {
            throw new GraphQlInputException(__('Invalid input. Product SKU and email are required.'));
        }

        $input = $args['input'];
        $productSku = $input['product_sku'];
        $email = $input['email'];
        $threshold = $input['threshold'] ?? null;

        try {
            // Verify that the product exists
            $this->productRepository->get($productSku);

            // Create the notification
            $notification = $this->notificationRepository->subscribe($productSku, $email, $threshold);

            return [
                'notification_id' => $notification->getId(),
                'product_sku' => $notification->getProductSku(),
                'email' => $notification->getEmail(),
                'threshold' => $notification->getThreshold(),
                'created_at' => $notification->getCreatedAt()
            ];
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new GraphQlInputException(__('The product with SKU "%1" does not exist.', $productSku));
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
