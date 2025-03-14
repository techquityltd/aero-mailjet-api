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

| **Property Name**  | **Type**   | **Description** |
|-------------------|-----------|------------------------------------------------|
| `firstname`      | Text      | Customer's first name |
| `name`           | Text      | Customer's full name |
| `cart_items`     | Text      | JSON containing cart item details (name, price, quantity) |
| `cart_total`     | Float     | Total value of the cart |
| `currency`       | String    | Cart currency (e.g., GBP, USD) |
| `currency_symbol` | String   | Currency symbol (e.g., £, $) |
| `cart_count`     | Integer   | Number of items in the cart |
| `basket_only`    | Boolean   | Indicates if the customer should only receive basket-related emails |
| `last_activity`  | Date      | Timestamp of the last cart update |
| `last_ordered_at` | Date     | Timestamp of the last completed order |

These properties can be used in Mailjet email templates to create personalized abandoned cart emails.
