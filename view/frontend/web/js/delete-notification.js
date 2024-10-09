define(['jquery', 'Magento_Ui/js/modal/confirm'], function ($, confirm) {
    'use strict';

    return function (config) {
        $(document).on('click', '.action.delete', function (event) {
            event.preventDefault();
            var notificationId = $(this).data('notification-id');

            confirm({
                content: $.mage.__(
                    'Are you sure you want to delete this notification?'
                ),
                actions: {
                    confirm: function () {
                        $.ajax({
                            url: config.deleteUrl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id: notificationId
                            },
                            showLoader: true,
                            success: function (response) {
                                if (response.success) {
                                    $(
                                        '[data-notification-id="' +
                                            notificationId +
                                            '"]'
                                    )
                                        .closest('tr')
                                        .remove();
                                    if (
                                        $('#my-notifications-table tbody tr')
                                            .length === 0
                                    ) {
                                        $('.price-drop-notifications').html(
                                            '<p class="message info empty"><span>' +
                                                $.mage.__(
                                                    'You have no price drop notifications.'
                                                ) +
                                                '</span></p>'
                                        );
                                    }
                                } else {
                                    alert({
                                        content: response.message
                                    });
                                }
                            },
                            error: function () {
                                alert({
                                    content: $.mage.__(
                                        'An error occurred while deleting the notification. Please try again later.'
                                    )
                                });
                            }
                        });
                    }
                }
            });
        });
    };
});
