<?php
declare(strict_types=1);

namespace MumzWorld\PriceDropNotification\Model\Queue;

use MumzWorld\PriceDropNotification\Api\Data\PriceDropNotificationMessageInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Handles processing of price drop notification messages from the queue
 */
class PriceDropNotificationHandler
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    /**
     * Process a price drop notification message
     *
     * @param PriceDropNotificationMessageInterface $message
     * @return void
     */
    public function process(PriceDropNotificationMessageInterface $message): void
    {
        try {
            $product = $this->productRepository->getById($message->getProductId());
            $store = $this->storeManager->getStore();

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->getEmailTemplate())
                ->setTemplateOptions(['area' => 'frontend', 'store' => $store->getId()])
                ->setTemplateVars([
                    'product_name' => $product->getName(),
                    'product_url' => $product->getProductUrl(),
                    'old_price' => $message->getOldPrice(),
                    'new_price' => $message->getNewPrice(),
                    'store' => $store
                ])
                ->setFrom($this->getSenderEmail())
                ->addTo($message->getCustomerEmail())
                ->getTransport();

            $transport->sendMessage();

            $this->logger->info('Price drop notification email sent', [
                'product_id' => $message->getProductId(),
                'customer_email' => $message->getCustomerEmail()
            ]);
        } catch (MailException $e) {
            $this->logger->error('Failed to send price drop notification email: ' . $e->getMessage(), [
                'product_id' => $message->getProductId(),
                'customer_email' => $message->getCustomerEmail(),
                'exception' => $e
            ]);
        } catch (\Exception $e) {
            $this->logger->critical('Unexpected error processing price drop notification: ' . $e->getMessage(), [
                'product_id' => $message->getProductId(),
                'customer_email' => $message->getCustomerEmail(),
                'exception' => $e
            ]);
        }
    }

    /**
     * Get email template ID from configuration
     *
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return $this->scopeConfig->getValue(
            'price_drop_notification/email/template',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get sender email from configuration
     *
     * @return string
     * @throws LocalizedException
     */
    private function getSenderEmail(): string
    {
        $sender = $this->scopeConfig->getValue(
            'price_drop_notification/email/sender',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $senderEmail = $this->scopeConfig->getValue(
            "trans_email/ident_{$sender}/email",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$senderEmail) {
            throw new LocalizedException(__('Sender email is not configured'));
        }

        return $senderEmail;
    }
}
