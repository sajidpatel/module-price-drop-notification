<?php
namespace MumzWorld\PriceDropNotification\Controller\Notification;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use MumzWorld\PriceDropNotification\Model\NotificationFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use MumzWorld\PriceDropNotification\Model\NotificationFactory;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Subscribe extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * Undocumented function
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param NotificationFactory $notificationFactory
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        NotificationFactory $notificationFactory,
        Session $customerSession,
        ProductRepositoryInterface $productRepository
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->notificationFactory = $notificationFactory;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;

        parent::__construct($context);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $result->setData(['success' => false, 'message' => __('Please login to subscribe to price drop notifications.')]);
        }

        $productId = $this->getRequest()->getParam('product_id');
        $customerId = $this->customerSession->getCustomerId();
        $customerEmail = $this->customerSession->getCustomer()->getEmail();

        try {
            $notification = $this->notificationFactory->create();
            $notification->setProductId($productId)
                ->setCustomerId($customerId)
                ->setEmail($customerEmail)
                ->save();

            return $result->setData(['success' => true, 'message' => __('You have successfully subscribed to price drop notifications for this product.')]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => __('An error occurred while subscribing. Please try again later.')]);
        }
    }
}
