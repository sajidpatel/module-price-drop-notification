<?php
namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use SajidPatel\PriceDropNotification\Model\NotificationRepository;

class UnsubscribeFromPriceDrop implements ResolverInterface
{
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['input']) || !isset($args['input']['product_sku']) || !isset($args['input']['email'])) {
            throw new GraphQlInputException(__('Invalid input. Product SKU and email are required.'));
        }

        $productSku = $args['input']['product_sku'];
        $email = $args['input']['email'];

        try {
            $result = $this->notificationRepository->unsubscribe($productSku, $email);

            return [
                'success' => $result,
                'message' => $result ? 'Successfully unsubscribed.' : 'No notification found for this product and email.'
            ];
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
