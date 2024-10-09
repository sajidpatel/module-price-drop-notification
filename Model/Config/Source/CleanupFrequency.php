<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CleanupFrequency implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'daily', 'label' => __('Daily')],
            ['value' => 'weekly', 'label' => __('Weekly')],
            ['value' => 'monthly', 'label' => __('Monthly')]
        ];
    }
}
