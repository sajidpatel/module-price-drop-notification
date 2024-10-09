<?php
declare(strict_types=1);

namespace MumzWorld\PriceDropNotification\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class PriceDropNotification extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->arrayManager->set(
            'product-details/children/container_enable_price_drop_notification/arguments/data/config',
            $meta,
            [
                'component' => 'Magento_Ui/js/form/element/single-checkbox',
                'formElement' => 'checkbox',
                'componentType' => 'field',
                'prefer' => 'toggle',
                'dataScope' => 'enable_price_drop_notification',
                'label' => __('Enable Price Drop Notification'),
                'valueMap' => [
                    'false' => '0',
                    'true' => '1'
                ],
                'default' => '1'
            ]
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
