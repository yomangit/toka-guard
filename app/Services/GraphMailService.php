<?php

namespace App\Services;

use Microsoft\Graph\GraphServiceClient;
use Microsoft\Graph\Generated\Models;

class GraphMailService
{
    protected GraphServiceClient $graph;

    public function __construct()
    {
        $token = $this->getAccessToken();

        // Pakai arrow function (fn) agar lebih ringkas
        $this->graph = new GraphServiceClient(fn () => $token);
    }

    protected function getAccessToken(): string
    {
        $tenantId = config('services.msgraph.tenant_id');
        $clientId = config('services.msgraph.client_id');
        $clientSecret = config('services.msgraph.client_secret');

        $url = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";

        $response = \Http::asForm()->post($url, [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'https://graph.microsoft.com/.default',
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    public function sendMail(string $fromUserId, string $to, string $subject, string $body): void
    {
        // Buat objek Message
        $message = new Models\Message();
        $message->setSubject($subject);

        $messageBody = new Models\ItemBody();
        $messageBody->setContentType(new Models\BodyType(Models\BodyType::HTML));
        $messageBody->setContent($body);
        $message->setBody($messageBody);

        $recipient = new Models\Recipient();
        $emailAddress = new Models\EmailAddress();
        $emailAddress->setAddress($to);
        $recipient->setEmailAddress($emailAddress);

        $message->setToRecipients([$recipient]);

        // Bungkus message dalam request body
        $sendMailBody = new Models\SendMailPostRequestBody();
        $sendMailBody->setMessage($message);
        $sendMailBody->setSaveToSentItems(true);

        // Kirim email via Graph
        $this->graph
            ->users()
            ->byUserId($fromUserId)
            ->sendMail()
            ->post($sendMailBody);
    }
}
