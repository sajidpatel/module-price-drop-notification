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

/**
 * Resolver for subscribing to price drop notifications.
 */
class SubscribeToPriceDrop implements ResolverInterface
{
    private ProductRepositoryInterface $productRepository;
    private NotificationRepositoryInterface $notificationRepository;
    private CustomerSession $customerSession;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param NotificationRepositoryInterface $notificationRepository
     * @param CustomerSession $customerSession
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        NotificationRepositoryInterface $notificationRepository,
        CustomerSession $customerSession
    ) {
        $this->productRepository = $productRepository;
        $this->notificationRepository = $notificationRepository;
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
        $this->validateInput($args);

        $input = $args['input'];
        $productSku = $input['product_sku'];
        $threshold = $input['threshold'];
        $providedEmail = $input['email'] ?? null;

        try {
            $this->validateProduct($productSku);
            $email = $this->getVerifiedEmail($providedEmail);

            $notification = $this->notificationRepository->subscribe(
                $productSku,
                $email,
                $threshold
            );

            return [
                'notification_id' => $notification->getId(),
                'product_sku' => $notification->getProductSku(),
                'email' => $notification->getEmail(),
                'threshold' => $notification->getThreshold(),
                'created_at' => $notification->getCreatedAt()
            ];
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }

    /**
     * Validate the input arguments.
     *
     * @param array|null $args
     * @throws GraphQlInputException
     */
    private function validateInput(?array $args): void
    {
        if (!isset($args['input'])
            || !isset($args['input']['product_sku'])
            || !isset($args['input']['threshold'])
        ) {
            throw new GraphQlInputException(
                __('Invalid input. Product SKU and threshold are required.')
            );
        }
    }

    /**
     * Validate that the product exists.
     *
     * @param string $productSku
     * @throws GraphQlInputException
     */
    private function validateProduct(string $productSku): void
    {
        try {
            $this->productRepository->get($productSku);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new GraphQlInputException(
                __('The product with SKU "%1" does not exist.', $productSku)
            );
        }
    }

    /**
     * Get and verify the email for the notification.
     *
     * @param string|null $providedEmail
     * @return string
     * @throws GraphQlInputException
     * @throws GraphQlAuthorizationException
     */
    private function getVerifiedEmail(?string $providedEmail): string
    {
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId) {
            $customerEmail = $this->customerSession->getCustomer()->getEmail();
            if ($providedEmail && $providedEmail !== $customerEmail) {
                throw new GraphQlAuthorizationException(
                    __('The provided email does not match the customer\'s email on file.')
                );
            }
            return $customerEmail;
        }

        if (!$providedEmail) {
            throw new GraphQlInputException(
                __('Email is required for guest users.')
            );
        }

        return $providedEmail;
    }
}
