<?php

namespace SajidPatel\PriceDropNotification\Plugin;

use Magento\Framework\App\Response\Http as ResponseHttp;

class CorsHeaders
{
    public function afterSendResponse(ResponseHttp $subject)
    {
        $subject->setHeader('Access-Control-Allow-Origin', '*');
        $subject->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $subject->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        return $subject;
    }
}
