# MumzWorld Price Drop Notification Module for Magento 2

## Overview

The MumzWorld Price Drop Notification module for Magento 2 allows customers to subscribe to price drop alerts for specific products. When the price of a subscribed product decreases, the system automatically sends an email notification to the customer.

## Features

- Enable/disable price drop notifications globally or per product
- Customers can subscribe to price drop notifications from the product page
- Admin configuration for email templates and notification settings
- Automated email notifications when prices drop
- GraphQL API support for headless frontend integration
- Cron jobs for processing notifications and cleaning up old data

## Installation

1. Create a directory for the module: `app/code/MumzWorld/PriceDropNotification`
2. Copy the module files into this directory
3. Enable the module by running: `bin/magento module:enable MumzWorld_PriceDropNotification`
4. Run the Magento setup upgrade: `bin/magento setup:upgrade`
5. Flush the cache: `bin/magento cache:flush`

## Configuration

1. Go to Stores > Configuration > Catalog > Price Drop Notification
2. Enable the module and configure email settings
3. Set up cron jobs for processing notifications and cleanup

## Usage

### Frontend

Customers can subscribe to price drop notifications by clicking the "Notify me when price drops" button on product pages (for products with the feature enabled).

### Admin

1. Enable/disable price drop notifications for specific products in the product edit page
2. View and manage price drop notification subscriptions in the admin panel

### GraphQL API

The module provides GraphQL mutations and queries for headless frontend integration:

- `subscribeToPriceDropNotification`: Subscribe to a price drop notification
- `unsubscribeFromPriceDropNotification`: Unsubscribe from a price drop notification
- `customerPriceDropNotifications`: Fetch a customer's price drop notifications

## Dependencies

- Magento_Catalog
- Magento_Customer
- Magento_Email

## Support

For issues, feature requests, or questions, please open an issue on the GitHub repository or contact MumzWorld support.

## License

[Open Software License (OSL 3.0)](https://opensource.org/licenses/OSL-3.0)

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request with your changes.