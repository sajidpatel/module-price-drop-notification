<?php
namespace SajidPatel\PriceDropNotification\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CronConfig
{
    const XML_PATH_QUEUE_PROCESS_FREQUENCY = 'price_drop_notification/cron_schedule/queue_process_frequency';
    const XML_PATH_CLEANUP_FREQUENCY = 'price_drop_notification/cron_schedule/cleanup_frequency';

    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getQueueProcessSchedule()
    {
        $frequency = $this->scopeConfig->getValue(self::XML_PATH_QUEUE_PROCESS_FREQUENCY, ScopeInterface::SCOPE_STORE);
        return $frequency ? "*/$frequency * * * *" : '*/5 * * * *'; // Default to every 5 minutes if not set
    }

    public function getCleanupSchedule()
    {
        $frequency = $this->scopeConfig->getValue(self::XML_PATH_CLEANUP_FREQUENCY, ScopeInterface::SCOPE_STORE);
        switch ($frequency) {
            case 'daily':
                return '0 0 * * *';
            case 'weekly':
                return '0 0 * * 0';
            case 'monthly':
                return '0 0 1 * *';
            default:
                return '0 0 * * *'; // Default to daily if not set
        }
    }
}
