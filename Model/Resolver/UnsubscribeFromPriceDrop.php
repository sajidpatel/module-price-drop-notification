<?php
declare(strict_types=1);

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
use Magento\Framework\Validator\EmailAddress as EmailValidator;

class UnsubscribeFromPriceDrop implements ResolverInterface
{
    private $productRepository;
    private $notificationRepository;
    private $customerSession;
    private $customerRepository;
    private $emailValidator;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        NotificationRepositoryInterface $notificationRepository,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        EmailValidator $emailValidator
    ) {
        $this->productRepository = $productRepository;
        $this->notificationRepository = $notificationRepository;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->emailValidator = $emailValidator;
    }

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
        $email = $this->getValidatedEmail($input['email'] ?? null);

        try {
            $this->validateProduct($productSku);
            $result = $this->notificationRepository->unsubscribe(
                $productSku,
                $email
            );

            return $this->formatResult($result);
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }

    private function validateInput(array $args): void
    {
        if (!isset($args['input']) || !isset($args['input']['product_sku'])) {
            throw new GraphQlInputException(
                __('Invalid input. Product SKU is required.')
            );
        }
    }

    private function getValidatedEmail(?string $inputEmail): string
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            $customer = $this->customerRepository->getById($customerId);
            $email = $customer->getEmail();

            if ($inputEmail && $inputEmail !== $email) {
                throw new GraphQlAuthorizationException(
                    __('Provided email does not match the logged-in customer.')
                );
            }
        } elseif (!$inputEmail) {
            throw new GraphQlInputException(
                __('Email is required for guest users.')
            );
        } else {
            $email = $inputEmail;
        }

        if (!$this->emailValidator->isValid($email)) {
            throw new GraphQlInputException(__('Invalid email address.'));
        }

        return $email;
    }

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

    private function formatResult(bool $result): array
    {
        return [
            'success' => $result,
            'message' => $result
                ? __('Successfully unsubscribed from price drop notifications.')
                : __('No active subscription found for the given product and email.')
        ];
    }
}
