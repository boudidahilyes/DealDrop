<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private $twilioClient;

    public function __construct(string $accountSid, string $authToken)
    {
        $this->twilioClient = new Client($accountSid, $authToken);
    }

    public function sendSms(string $to, string $message)
    {
        $this->twilioClient->messages->create(
            $to,
            [
                'from' => +17653357108,
                'body' => $message,
            ]
        );
    }
}
