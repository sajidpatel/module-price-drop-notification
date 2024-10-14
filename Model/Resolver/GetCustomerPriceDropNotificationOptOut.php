<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Resolver for getting customer's price drop notification opt-out status
 */
class GetCustomerPriceDropNotificationOptOut implements ResolverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerSession $customerSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
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
        $result = null;

        try {
            if ($this->customerSession->isLoggedIn()) {
                $result = $this->resolveFromLoggedInCustomer();
            }
        } catch (\Exception $e) {
            $this->logger->error('Error resolving price drop notification opt-out status: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }

        return $result;
    }

    /**
     * Resolve opt-out status from customer model
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return bool
     */
    private function resolveFromCustomerModel($customer): bool
    {
        $attribute = $customer->getCustomAttribute('price_drop_notification_opt_out');
        return $attribute ? (bool)$attribute->getValue() : false;
    }

    /**
     * Resolve opt-out status for logged-in customer
     *
     * @return bool
     * @throws LocalizedException
     */
    private function resolveFromLoggedInCustomer(): bool
    {
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);
        return $this->resolveFromCustomerModel($customer);
    }
}
