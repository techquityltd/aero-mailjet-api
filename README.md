# AeroMailjetApi

## Introduction
AeroMailjetApi is a Laravel package for integrating Mailjet with an AeroCommerce store. This package allows you to:
- Add customers to Mailjet contact lists.
- Track abandoned carts and store cart details as contact properties.
- Remove contacts from the abandoned basket list when an order is completed.

## Installation

1. **Install via Composer** (if applicable):
   ```bash
   composer require techquity/aero-mailjet-api
   ```

2. **Set up Mailjet API Credentials** in Aero mailjet api settings:
   ```env
   MAILJET_KEY=your_mailjet_api_key
   MAILJET_SECRET=your_mailjet_api_secret
   MAILJET_ABANDONED_BASKET_LIST_ID=your_list_id
   ```

## Contact Properties in Mailjet
The package stores abandoned cart details in Mailjet contact properties:

| Property Name   | Type    | Description                        |
|---------------|--------|--------------------------------|
| firstname    | Text   | Customer's first name |
| name    | Text   | Customer's full name |
| cart_items    | Text   | JSON of cart items (name, price, quantity) |
| cart_total    | Float  | Total value of the cart        |
| currency      | String | Currency of the cart (e.g., GBP, USD) |
| currency_symbol     | String | Currency symbol of the cart (e.g., £, $) |
| cart_count    | Integer| Number of items in the cart    |
| last_activity | Date   | Timestamp of last cart update |
| last_ordered_at | Date   | Timestamp of last order |

These properties can be used in Mailjet email templates to create personalized abandoned cart emails.
