<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Block\Product\View;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;

/**
 * Price Drop Notification Button Block
 */
class PriceDropButton extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Product|null
     */
    protected $product;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get current product
     *
     * @return Product
     * @throws LocalizedException
     */
    public function getProduct(): Product
    {
        if ($this->product === null) {
            $this->product = $this->registry->registry('product');

            if (!$this->product || !$this->product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->product;
    }

    /**
     * Check if price drop notification is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'price_drop_notification/general/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get subscription URL
     *
     * @return string
     */
    public function getSubscribeUrl(): string
    {
        return $this->getUrl('pricedrop/notification/subscribe');
    }
}
