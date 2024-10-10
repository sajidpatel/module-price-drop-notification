<?php
namespace SajidPatel\PriceDropNotification\Plugin;

use Magento\Cron\Model\Config\Backend\Product\Alert;
use SajidPatel\PriceDropNotification\Model\CronConfig;

class CronConfigPlugin
{
    protected $cronConfig;

    public function __construct(CronConfig $cronConfig)
    {
        $this->cronConfig = $cronConfig;
    }

    public function afterSave(Alert $subject, $result)
    {
        $subject->getResource()->saveConfig(
            'crontab/default/jobs/sajidpatel_price_drop_notification_process_queue/schedule/cron_expr',
            $this->cronConfig->getQueueProcessSchedule(),
            $subject->getScope(),
            $subject->getScopeId()
        );

        $subject->getResource()->saveConfig(
            'crontab/default/jobs/sajidpatel_price_drop_notification_cleanup/schedule/cron_expr',
            $this->cronConfig->getCleanupSchedule(),
            $subject->getScope(),
            $subject->getScopeId()
        );

        return $result;
    }
}
