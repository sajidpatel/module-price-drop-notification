<?php
namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use SajidPatel\PriceDropNotification\Model\NotificationRepository;

class PriceDropNotifications implements ResolverInterface
{
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
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
        /** @var \Magento\GraphQl\Model\Query\Context $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $customerId = $context->getUserId();

        try {
            $notifications = $this->notificationRepository->getByCustomerId($customerId);

            return array_map(function ($notification) {
                return [
                    'notification_id' => $notification->getId(),
                    'product_sku' => $notification->getProductSku(),
                    'email' => $notification->getEmail(),
                    'threshold' => $notification->getThreshold(),
                    'created_at' => $notification->getCreatedAt()->format(\DateTime::ATOM)
                ];
            }, $notifications);
        } catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        }
    }
}
