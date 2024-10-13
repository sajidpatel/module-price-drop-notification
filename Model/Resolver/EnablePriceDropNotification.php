<?php
namespace SajidPatel\PriceDropNotification\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class EnablePriceDropNotification implements ResolverInterface
{
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $product = $value['model'];

        $enablePriceDropNotification =
            $product->getData('enable_price_drop_notification');

        // Convert to boolean
        return (bool) $enablePriceDropNotification;
    }
}
