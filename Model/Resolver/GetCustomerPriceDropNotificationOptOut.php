<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;

class GetCustomerPriceDropNotificationOptOut implements ResolverInterface
{
    private $customerRepository;
    private $customerSession;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        // Case 1: Resolving as part of Customer type
        if (isset($value['model'])) {
            /** @var \Magento\Customer\Model\Data\Customer $customer */
            $customer = $value['model'];
            $attribute = $customer->getCustomAttribute('price_drop_notification_opt_out');
            return $attribute ? (bool)$attribute->getValue() : false;
        }

        // Case 2: Resolving as standalone query
        if (!$this->customerSession->isLoggedIn()) {
            return null; // Return null if no customer is logged in
        }

        $customerId = $this->customerSession->getCustomerId();
        try {
            $customer = $this->customerRepository->getById($customerId);
            $attribute = $customer->getCustomAttribute('price_drop_notification_opt_out');
            return $attribute ? (bool)$attribute->getValue() : false;
        } catch (\Exception $e) {
            // Log the exception if needed
            return null;
        }
    }
}
