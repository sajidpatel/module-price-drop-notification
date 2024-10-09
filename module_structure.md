# MumzWorld_PriceDropNotification Module Structure

```
MumzWorld_PriceDropNotification/
├── Api/
│   └── Data/
│       └── PriceDropNotificationMessageInterface.php
├── Cron/
│   ├── CleanupNotifications.php
│   └── ProcessPriceDropNotificationQueue.php
├── Model/
│   ├── Config/
│   │   └── Source/
│   │       └── CleanupFrequency.php
│   ├── Queue/
│   │   ├── PriceDropNotificationHandler.php
│   │   └── PriceDropNotificationPublisher.php
│   ├── ResourceModel/
│   │   ├── Notification/
│   │   │   └── Collection.php
│   │   └── Notification.php
│   ├── Notification.php
│   └── PriceDropNotificationMessage.php
├── Observer/
│   └── PriceChange.php
├── Resolver/
│   ├── CustomerPriceDropNotifications.php
│   ├── FilterArgument/
│   │   └── PriceDropNotification.php
│   ├── SubscribeToPriceDropNotification.php
│   └── UnsubscribeFromPriceDropNotification.php
├── Setup/
│   └── Patch/
│       └── Data/
│           └── AddPriceDropNotificationAttribute.php
├── Ui/
│   └── DataProvider/
│       └── Product/
│           └── Form/
│               └── Modifier/
│                   └── PriceDropNotification.php
├── etc/
│   ├── adminhtml/
│   │   ├── di.xml
│   │   └── system.xml
│   ├── config.xml
│   ├── crontab.xml
│   ├── db_schema.xml
│   ├── di.xml
│   ├── email_templates.xml
│   ├── events.xml
│   ├── frontend/
│   │   └── routes.xml
│   ├── module.xml
│   ├── queue_consumer.xml
│   ├── queue_publisher.xml
│   ├── queue_topology.xml
│   └── schema.graphqls
├── view/
│   ├── adminhtml/
│   │   └── ui_component/
│   │       └── product_form.xml
│   └── frontend/
│       ├── email/
│       │   └── price_drop_notification.html
│       ├── layout/
│       │   ├── catalog_product_view.xml
│       │   └── customer_account.xml
│       ├── templates/
│       │   └── product/
│       │       └── view/
│       │           └── price_drop_button.phtml
│       └── web/
│           └── js/
│               └── price-drop-notify.js
├── composer.json
├── LICENSE.txt
├── README.md
└── registration.php
```

This structure reflects the current state of the MumzWorld_PriceDropNotification module, including GraphQL resolvers and other components we've added.