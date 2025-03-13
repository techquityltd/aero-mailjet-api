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
        // Check if the contact exists in Mailjet
        $contactExists = MailjetApi::checkContactExists($this->email);
        
        if ($contactExists) {
            // If the contact exists, do not change their marketing subscription status
            return;
        }

        // If they are new, add them with "excluded" status to avoid marketing emails
        MailjetApi::addContact($this->email, 'excluded');
    }
}
