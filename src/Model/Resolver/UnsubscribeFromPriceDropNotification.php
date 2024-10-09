<?php
declare(strict_types=1);

namespace MumzWorld\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use MumzWorld\PriceDropNotification\Model\NotificationFactory;
use MumzWorld\PriceDropNotification\Model\ResourceModel\Notification as NotificationResource;
use Magento\Customer\Model\Session;

class UnsubscribeFromPriceDropNotification implements ResolverInterface
{
    private $notificationFactory;
    private $notificationResource;
    private $customerSession;

    public function __construct(
        NotificationFactory $notificationFactory,
        NotificationResource $notificationResource,
        Session $customerSession
    ) {
        $this->notificationFactory = $notificationFactory;
        $this->notificationResource = $notificationResource;
        $this->customerSession = $customerSession;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->customerSession->isLoggedIn()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        if (!isset($args['notification_id'])) {
            throw new GraphQlInputException(__('Notification ID is required.'));
        }

        try {
            $notification = $this->notificationFactory->create();
            $this->notificationResource->load($notification, $args['notification_id']);

            if ($notification->getCustomerId() != $this->customerSession->getCustomerId()) {
                throw new GraphQlAuthorizationException(__('You are not authorized to delete this notification.'));
            }

            $this->notificationResource->delete($notification);

            return [
                'success' => true,
                'message' => __('You have successfully unsubscribed from price drop notifications for this product.')
            ];
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
