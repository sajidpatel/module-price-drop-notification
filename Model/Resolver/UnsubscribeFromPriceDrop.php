<?php
namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use SajidPatel\PriceDropNotification\Api\NotificationRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;

class UnsubscribeFromPriceDrop implements ResolverInterface
{
    private $productRepository;
    private $notificationRepository;
    private $customerSession;
    private $customerRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        NotificationRepositoryInterface $notificationRepository,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->productRepository = $productRepository;
        $this->notificationRepository = $notificationRepository;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['input']) || !isset($args['input']['product_sku'])) {
            throw new GraphQlInputException(__('Invalid input. Product SKU is required.'));
        }

        $input = $args['input'];
        $productSku = $input['product_sku'];
        $email = $input['email'] ?? null;

        try {
            // Verify that the product exists
            $this->productRepository->get($productSku);

            // Check if the customer is logged in
            if (!$this->customerSession->isLoggedIn()) {
                // Guest user
                if (!$email) {
                    throw new GraphQlInputException(__('Email is required for guest users.'));
                }
            } else {
                // Logged-in customer
                $customerId = $this->customerSession->getCustomerId();
                $customer = $this->customerRepository->getById($customerId);
                $email = $customer->getEmail();
            }

            // Unsubscribe from the notification
            $result = $this->notificationRepository->unsubscribe($productSku, $email);

            return [
                'success' => $result,
                'message' => $result
                    ? __('Successfully unsubscribed from price drop notifications for the product.')
                    : __('No active subscription found for the given product and email.')
            ];
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new GraphQlInputException(__('The product with SKU "%1" does not exist.', $productSku));
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
