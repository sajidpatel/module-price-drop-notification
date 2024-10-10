<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Plugin\Model;

use Magento\Customer\Model\Customer;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerPlugin
{
    /**
     * Add price drop notification opt-out attribute to customer model
     *
     * @param Customer $subject
     * @param CustomerInterface $result
     * @return CustomerInterface
     */
    public function afterGetDataModel(Customer $subject, CustomerInterface $result): CustomerInterface
    {
        $priceDrop = $subject->getData('price_drop_notification_opt_out');
        $result->setCustomAttribute('price_drop_notification_opt_out', $priceDrop);
        return $result;
    }
}
