<?php

namespace SajidPatel\PriceDropNotification\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Notification extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('price_drop_notification', 'notification_id');
    }
}
