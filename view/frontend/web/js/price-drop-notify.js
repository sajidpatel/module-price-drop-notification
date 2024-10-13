define(['jquery', 'Magento_Ui/js/modal/alert'], function ($, alert) {
    'use strict';

    return function (config) {
        $(config.element).on('click', function () {
            $.ajax({
                url: config.subscribeUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    product_sku: config.productSku
                },
                success: function (response) {
                    alert({
                        title: response.success
                            ? $.mage.__('Success')
                            : $.mage.__('Error'),
                        content: response.message
                    });
                },
                error: function () {
                    alert({
                        title: $.mage.__('Error'),
                        content: $.mage.__(
                            'An error occurred. Please try again later.'
                        )
                    });
                }
            });
        });
    };
});
