<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class PriceDropNotification extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;

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
            'product-details/children/container_price_drop_notification',
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'container',
                            'formElement' => 'container',
                            'component' => 'Magento_Ui/js/form/components/group',
                            'label' => __('Price Drop Notification'),
                            'sortOrder' => 50,
                        ],
                    ],
                ],
                'children' => [
                    'enable_price_drop_notification' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType' => 'boolean',
                                    'formElement' => 'checkbox',
                                    'componentType' => 'field',
                                    'component' => 'Magento_Ui/js/form/element/single-checkbox',
                                    'prefer' => 'toggle',
                                    'dataScope' => 'enable_price_drop_notification',
                                    'label' => __('Enable Price Drop Notification'),
                                    'valueMap' => [
                                        'false' => '0',
                                        'true' => '1'
                                    ],
                                    'default' => '1',
                                    'sortOrder' => 10,
                                ],
                            ],
                        ],
                    ],
                ],
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
