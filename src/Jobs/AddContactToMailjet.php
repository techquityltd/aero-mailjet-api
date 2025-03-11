<?php

namespace Techquity\AeroMailjetApi\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Techquity\AeroMailjetApi\MailjetApi;

class AddContactToMailjet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function handle(): void
    {
        MailjetApi::addContact($this->email);
    }
}
