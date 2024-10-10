<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;

class TogglePriceDropNotificationOptOut implements ResolverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session $customerSession
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Session $customerSession
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
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
        if (!$this->customerSession->isLoggedIn()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);

        $optOut = $args['opt_out'] ?? false;
        $customer->setCustomAttribute('price_drop_notification_opt_out', $optOut);

        $this->customerRepository->save($customer);

        return [
            'customer_id' => $customerId,
            'opted_out' => $optOut
        ];
    }
}
