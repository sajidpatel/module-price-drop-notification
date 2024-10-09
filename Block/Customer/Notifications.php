<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification\CollectionFactory;
use Magento\Customer\Model\Session;

class Notifications extends Template
{
    /**
     * @var CollectionFactory
     */
    private $notificationCollectionFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param CollectionFactory $notificationCollectionFactory
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $notificationCollectionFactory,
        Session $customerSession,
        array $data = []
    ) {
        $this->notificationCollectionFactory = $notificationCollectionFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Get customer's price drop notifications
     *
     * @return \SajidPatel\PriceDropNotification\Model\ResourceModel\Notification\Collection
     */
    public function getNotifications()
    {
        $customerId = $this->customerSession->getCustomerId();
        $collection = $this->notificationCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        return $collection;
    }

    /**
     * Get delete notification URL
     *
     * @param int $notificationId
     * @return string
     */
    public function getDeleteUrl($notificationId)
    {
        return $this->getUrl('pricedrop/notification/delete', ['id' => $notificationId]);
    }
}
