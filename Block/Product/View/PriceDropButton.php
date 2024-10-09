<?php
namespace SajidPatel\PriceDropNotification\Block\Product\View;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class PriceDropButton extends Template
{
    protected $scopeConfig;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'price_drop_notification/general/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSubscribeUrl()
    {
        return $this->getUrl('pricedrop/notification/subscribe');
    }
}
