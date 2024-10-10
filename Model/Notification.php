<?php

namespace SajidPatel\PriceDropNotification\Model;

use Magento\Framework\Model\AbstractModel;
use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification as ResourceModel;

class Notification extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}