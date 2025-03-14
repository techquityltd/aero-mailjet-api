<?php

namespace Techquity\AeroMailjetApi;

use Mailjet\Client;
use Mailjet\Resources;
use Illuminate\Support\Facades\Log;

class MailjetApi
{
    protected static function client(): Client
    {
        return new Client(setting('mailjet-api.key'), setting('mailjet-api.secret'), true, ['version' => 'v3']);
    }

    public static function checkContactExists(string $email): bool
    {
        $mj = self::client();
        
        $response = $mj->get(Resources::$Contact, ['id' => $email]);

        return $response->success();
    }

    /**
     * Add a contact to Mailjet.
     */
    public static function addContact(string $email, string $status = 'included'): void
    {
        try {
            $mj = self::client();

            // Add contact if not exists
            $response = $mj->post(Resources::$Contact, [
                'body' => [
                        'Email' => $email,
                        'IsExcludedFromCampaigns' => $status === 'included' ? false : true,
                    ]
            ]);

            if (!$response->success()) {
                Log::error("Mailjet addContact failed", ['email' => $email, 'response' => $response->getBody()]);
            }
        } catch (\Exception $e) {
            Log::error("Mailjet addContact error: " . $e->getMessage());
        }
    }

    /**
     * Add a contact to the abandoned basket list and update their cart data.
     */
    public static function addContactToAbandonedBasket(string $email, $order): void
    {
        try {
            $mj = self::client();
            $listId = setting('mailjet-api.abandoned_basket_list_id'); // Store List ID in config

            // Subscribe Contact to List
            $mj->post(Resources::$Listrecipient, [
                'body' => [
                    'ContactAlt' => $email,
                    'ListID' => $listId,
                    'IsActive' => true
                ]
            ]);

            // Convert cart items to JSON
            $cartItems = [];
            foreach ($order->items as $item) {
                $cartItems[] = [
                    'name' => $item->name,
                    'price' => number_format(($item->price / 100 ), 2),
                    'quantity' => $item->quantity
                ];
            }

            $customerName = $order->customer ? $order->customer->name : $order->shippingAddress->full_name;

            // Add cart details to contact properties
            $response = $mj->put(Resources::$Contactdata, [
                'id' => $email,
                'body' => [
                    'Data' => [
                        ['Name' => 'name', 'Value' => $customerName],
                        ['Name' => 'firstname', 'Value' => $order->shippingAddress->first_name],
                        ['Name' => 'cart_items', 'Value' => json_encode($cartItems)],
                        ['Name' => 'cart_total', 'Value' => number_format(($order->total / 100), 2)],
                        ['Name' => 'currency', 'Value' => $order->currency->code],
                        ['Name' => 'currency_symbol', 'Value' => $order->currency->symbol],
                        ['Name' => 'cart_count', 'Value' => count($order->items)],
                        ['Name' => 'basket_only', 'Value' => true],
                        ['Name' => 'last_activity', 'Value' => now()->toDateTimeString()],
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Mailjet addContactToAbandonedBasket error: " . $e->getMessage());
        }
    }

    public static function removeFromAbandonedBasket(string $email): void
    {
        try {
            $mj = self::client();
            $listId = setting('mailjet-api.abandoned_basket_list_id');

            // Find ListRecipient ID for the contact
            $response = $mj->get(Resources::$Listrecipient, [
                'filters' => [
                    'ContactAlt' => $email,
                    'ListID' => $listId
                ]
            ]);

            if ($response->success() && !empty($response->getBody()['Data'])) {
                $listRecipientId = $response->getBody()['Data'][0]['ID'];

                // Remove contact from the abandoned basket list
                $mj->delete(Resources::$Listrecipient, ['id' => $listRecipientId]);
            }

            // Clear abandoned basket properties
            $mj->put(Resources::$Contactdata, [
                'id' => $email,
                'body' => [
                    'Data' => [
                        ['Name' => 'cart_items', 'Value' => ''],
                        ['Name' => 'cart_total', 'Value' => ''],
                        ['Name' => 'cart_count', 'Value' => '0'],
                        ['Name' => 'cart_url', 'Value' => ''],
                        ['Name' => 'basket_only', 'Value' => false],
                        ['Name' => 'last_ordered_at', 'Value' => now()->toDateTimeString()],
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("Mailjet removeFromAbandonedBasket error: " . $e->getMessage());
        }
    }

}
