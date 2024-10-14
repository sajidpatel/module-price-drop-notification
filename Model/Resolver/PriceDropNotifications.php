<?php
namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use SajidPatel\PriceDropNotification\Model\NotificationRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class PriceDropNotifications implements ResolverInterface
{
    private $notificationRepository;
    private $customerSession;
    private $customerRepository;
    private $searchCriteriaBuilder;

    public function __construct(
        NotificationRepository $notificationRepository,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
        $email = null;
        $customerId = null;

        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            $customer = $this->customerRepository->getById($customerId);
            $email = $customer->getEmail();
        } else {
            if (!isset($args['email'])) {
                throw new GraphQlInputException(__('Email is required for guest users.'));
            }
            $email = $args['email'];
        }

        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('email', $email)
                ->create();

            $searchResults = $this->notificationRepository->getList($searchCriteria);
            $notifications = $searchResults->getItems();

            return array_map(function ($notification) {
                return [
                    'notification_id' => $notification->getId(),
                    'product_sku' => $notification->getProductSku(),
                    'email' => $notification->getEmail(),
                    'threshold' => $notification->getThreshold(),
                    'created_at' => $notification->getCreatedAt()
                ];
            }, $notifications);
        } catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        }
    }
}
