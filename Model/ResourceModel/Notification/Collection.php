<?php

namespace SajidPatel\PriceDropNotification\Model\ResourceModel\Notification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SajidPatel\PriceDropNotification\Model\Notification as Model;
use SajidPatel\PriceDropNotification\Model\ResourceModel\Notification as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
