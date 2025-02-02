<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Controller\Notification;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session;
use SajidPatel\PriceDropNotification\Model\NotificationFactory;
use SajidPatel\PriceDropNotification\Model\Notification as NotificationResource;
use Psr\Log\LoggerInterface;

class Delete implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var NotificationResource
     */
    protected $notificationResource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param Session $customerSession
     * @param NotificationFactory $NotificationFactory
     * @param NotificationResource $notificationResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        Session $customerSession,
        NotificationFactory $notificationFactory,
        NotificationResource $notificationResource,
        LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->notificationFactory = $notificationFactory;
        $this->notificationResource = $notificationResource;
        $this->logger = $logger;
    }

    /**
     * Delete a price drop notification
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $responseData = ['success' => false, 'message' => __('An error occurred while deleting the notification.')];

        if ($this->customerSession->isLoggedIn()) {
            $notificationId = (int)$this->getRequest()->getParam('id');
            $customerId = $this->customerSession->getCustomerId();

            try {
                $notification = $this->notificationFactory->create();
                $this->notificationResource->load($notification, $notificationId);

                if ($notification->getCustomerId() == $customerId) {
                    $this->notificationResource->delete($notification);
                    $this->logger->info('Price drop notification deleted', [
                        'notification_id' => $notificationId,
                        'customer_id' => $customerId
                    ]);
                    $responseData = [
                        'success' => true,
                        'message' => __('Notification has been deleted successfully.')
                    ];
                } else {
                    $responseData['message'] = __('You are not authorized to delete this notification.');
                }
            } catch (\Exception $e) {
                $this->logger->error('Error deleting price drop notification: ' . $e->getMessage(), [
                    'exception' => $e,
                    'notification_id' => $notificationId
                ]);
                $responseData['message'] =
                    __('An error occurred while deleting the notification. Please try again later.');
            }
        } else {
            $responseData['message'] = __('Please login to delete notifications.');
        }

        return $result->setData($responseData);
    }
}
