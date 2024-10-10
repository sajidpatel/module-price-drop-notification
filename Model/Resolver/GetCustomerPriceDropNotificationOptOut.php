<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetCustomerPriceDropNotificationOptOut implements ResolverInterface
{
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            return null;
        }

        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = $value['model'];
        $attribute = $customer->getCustomAttribute('price_drop_notification_opt_out');

        return $attribute ? (bool)$attribute->getValue() : false;
    }
}
