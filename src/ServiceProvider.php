<?php

namespace Techquity\AeroMailjetApi;

use Aero\Checkout\Http\Responses\CheckoutCustomerSet;
use Aero\Checkout\Http\Responses\CheckoutOrderConfirmationPage;
use Aero\Common\Facades\Settings;
use Aero\Common\Providers\ModuleServiceProvider;
use Aero\Common\Settings\SettingGroup;
use Aero\Responses\ResponseBuilder;
use Techquity\AeroMailjetApi\Jobs\AddContactToMailjet;
use Techquity\AeroMailjetApi\Jobs\AddToAbandonedBasket;
use Techquity\AeroMailjetApi\Jobs\RemoveFromAbandonedBasket;

class ServiceProvider extends ModuleServiceProvider
{

    public function setup(): void
    {
        Settings::group('mailjet-api', function (SettingGroup $group) {
            $group->boolean('enabled')->default(true);
            $group->string('queue')->default('default');
            $group->string('key');
            $group->string('secret');
            $group->string('abandoned_basket_list_id');
        });

        CheckoutCustomerSet::extend(function (CheckoutCustomerSet $page) {

            if (!setting('mailjet-api.enabled')) {
                return;
            }

            $customer = $page->cart->customer();
            $order = $page->cart->order();
        
            if (optional($customer)->email) {
                AddContactToMailjet::dispatch($customer->email)->onQueue(setting('mailjet-api.queue'));
            }
        
            if ($customer->email && optional($order)->items) {
                AddToAbandonedBasket::dispatch($customer->email, $order)->onQueue(setting('mailjet-api.queue'));
            }
        });

        CheckoutOrderConfirmationPage::extend(function (ResponseBuilder $builder, \Closure $next) {

            if (!setting('mailjet-api.enabled')) {
                return $next($builder);
            }

            $order = $builder->getData('order');

            if (! $order) {
                return $next($builder);
            }
            
            if ($order->email) {
                RemoveFromAbandonedBasket::dispatch($order->email)->onQueue(setting('mailjet-api.queue'));
            }
        });
    }
}
