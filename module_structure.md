MumzWorld_PriceDropNotification/
├── src/
│   ├── Api/
│   │   └── Data/
│   │       └── PriceDropNotificationMessageInterface.php
│   ├── Block/
│   │   ├── Customer/
│   │   │   └── Notification.php
│   │   ├── Product/
│   │   │   └── View/
│   │   │       └── PriceDropButton.php
│   ├── Controller/
│   │   └── Notification/
│   │       ├── Delete.php
│   │       ├── Index.php
│   │       └── Subscribe.php
│   ├── Cron/
│   │   └── SendNotifications.php
│   ├── Model/
│   │   ├── Notification.php
│   │   └── ResourceModel/
│   │       ├── Notification.php
│   │       └── Notification/
│   │           └── Collection.php
│   └── Observer/
│       └── PriceChange.php
├── etc/
│   ├── adminhtml/
│   │   └── system.xml
│   ├── config.xml
│   ├── crontab.xml
│   ├── db_schema.xml
│   ├── di.xml
│   ├── events.xml
│   ├── frontend/
│   │   └── routes.xml
│   └── module.xml
├── view/
│   ├── adminhtml/
│   │   └── templates/
│   │       └── system/
│   │           └── config/
│   │               └── fieldset/
│   │                   └── hint.phtml
│   └── frontend/
│       ├── layout/
│       │   └── catalog_product_view.xml
│       ├── templates/
│       │   └── product/
│       │       └── view/
│       │           └── price_drop_button.phtml
│       └── web/
│           └── js/
│               └── price-drop-notify.js
├── composer.json
└── registration.php