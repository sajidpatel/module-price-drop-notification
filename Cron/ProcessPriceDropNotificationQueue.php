<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\MessageQueue\ConsumerFactory;
use Psr\Log\LoggerInterface;

class ProcessPriceDropNotificationQueue
{
    /**
     * @var ConsumerFactory
     */
    protected $consumerFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ProcessPriceDropNotificationQueue Constructor function
     *
     * @param ConsumerFactory $consumerFactory
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ConsumerFactory $consumerFactory,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->consumerFactory = $consumerFactory;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Process the price drop notification queue
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $consumer = $this->consumerFactory->get('PriceDropNotificationConsumer');
            $consumer->process(100); // Process up to 100 messages
            $this->logger->info('Price drop notification queue processed successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Error processing price drop notification queue: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    private function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('price_drop_notification/general/enabled');
    }
}
