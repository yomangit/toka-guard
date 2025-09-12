<?php

namespace App\Services;

use Microsoft\Graph\GraphServiceClient;
use Microsoft\Graph\Generated\Models\Message;
use Microsoft\Graph\Generated\Models\BodyType;
use Microsoft\Graph\Generated\Models\ItemBody;
use Microsoft\Graph\Generated\Models\Recipient;
use Microsoft\Graph\Generated\Models\EmailAddress;
use Microsoft\Graph\Generated\Models\SendMailPostRequestBody;
use Microsoft\Kiota\Authentication\Oauth\BaseBearerTokenAuthenticationProvider;


class GraphMailService
{
    protected GraphServiceClient $graph;

    public function __construct()
    {
        $token = $this->getAccessToken();

        // Gunakan AuthenticationProvider sesuai v2.47
        $authProvider = new BaseBearerTokenAuthenticationProvider($token);
        $this->graph = new GraphServiceClient($authProvider);
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
        // Build Message
        $message = new Message();
        $message->setSubject($subject);

        $messageBody = new ItemBody();
        $messageBody->setContentType(new BodyType(BodyType::HTML));
        $messageBody->setContent($body);
        $message->setBody($messageBody);

        $recipient = new Recipient();
        $emailAddress = new EmailAddress();
        $emailAddress->setAddress($to);
        $recipient->setEmailAddress($emailAddress);
        $message->setToRecipients([$recipient]);

        // Wrap in request body
        $sendMailBody = new SendMailPostRequestBody();
        $sendMailBody->setMessage($message);
        $sendMailBody->setSaveToSentItems(true);

        // Send email
        $this->graph
            ->users()
            ->byUserId($fromUserId)
            ->sendMail()
            ->post($sendMailBody);
    }
}
