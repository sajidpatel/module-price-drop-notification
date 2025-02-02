<?php
namespace SajidPatel\PriceDropNotification\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SajidPatel\PriceDropNotification\Api\Data\NotificationInterface;
use SajidPatel\PriceDropNotification\Api\Data\NotificationSearchResultsInterfaceFactory;
use SajidPatel\PriceDropNotification\Api\NotificationRepositoryInterface;
use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification as ResourceNotification;
use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;

class NotificationRepository implements NotificationRepositoryInterface
{
    protected $resource;
    protected $notificationFactory;
    protected $notificationCollectionFactory;
    protected $searchResultsFactory;

    public function __construct(
        ResourceNotification $resource,
        NotificationFactory $notificationFactory,
        NotificationCollectionFactory $notificationCollectionFactory,
        NotificationSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->notificationFactory = $notificationFactory;
        $this->notificationCollectionFactory = $notificationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function save(NotificationInterface $notification)
    {
        try {
            $this->resource->save($notification);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $notification;
    }

    public function getById($notificationId)
    {
        $notification = $this->notificationFactory->create();
        $this->resource->load($notification, $notificationId);
        if (!$notification->getId()) {
            throw new NoSuchEntityException(__('Notification with id "%1" does not exist.', $notificationId));
        }
        return $notification;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->notificationCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    public function delete(NotificationInterface $notification)
    {
        try {
            $this->resource->delete($notification);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById($notificationId)
    {
        return $this->delete($this->getById($notificationId));
    }

    public function getByCustomerId($customerId)
    {
        $collection = $this->notificationCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        return $collection->getItems();
    }

    /**
     * Subscribe customer to price drop notification
     *
     * @param string $productSku
     * @param string $email
     * @param float|null $threshold
     * @param int|null $customerId
     * @return NotificationInterface
     * @throws LocalizedException
     */
    public function subscribe($productSku, $email, $threshold = null, $customerId = null)
    {
        // Check if a notification already exists for this email and product SKU
        $existingNotification = $this->notificationCollectionFactory->create()
            ->addFieldToFilter('product_sku', $productSku)
            ->addFieldToFilter('email', $email);

        if ($customerId) {
            $existingNotification->addFieldToFilter('customer_id', $customerId);
        }

        $existingNotification = $existingNotification->getFirstItem();

        if ($existingNotification->getId()) {
            throw new LocalizedException(
                __('A notification already exists for this email and product SKU.')
            );
        }

        /** @var NotificationInterface $notification */
        $notification = $this->notificationFactory->create();
        $notification->setProductSku($productSku);
        $notification->setEmail($email);
        $notification->setThreshold($threshold);
        if ($customerId) {
            $notification->setCustomerId($customerId);
        }

        return $this->save($notification);
    }

    /**
     * Unsubscribe customer from price drop notification
     *
     * @param string $productSku
     * @param string $email
     * @return bool
     * @throws LocalizedException
     */
    public function unsubscribe($productSku, $email, $customerId = null)
    {
        $collection = $this->notificationCollectionFactory->create();
        $collection->addFieldToFilter('product_sku', $productSku)
            ->addFieldToFilter('email', $email);
        if ($customerId) {
            $collection->addFieldToFilter('customer_id', $customerId);
        }
        $notification = $collection->getFirstItem();
        if ($notification->getId()) {
            return $this->delete($notification);
        }
        return false;
    }
}
