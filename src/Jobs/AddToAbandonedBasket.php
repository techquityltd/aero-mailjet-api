<?php

namespace Techquity\AeroMailjetApi\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Techquity\AeroMailjetApi\MailjetApi;
use Aero\Cart\Models\Order;

class AddToAbandonedBasket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;
    protected Order $order;

    public function __construct(string $email, Order $order)
    {
        $this->email = $email;
        $this->order = $order;
    }

    public function handle(): void
    {
        MailjetApi::addContactToAbandonedBasket($this->email, $this->order);
    }
}
