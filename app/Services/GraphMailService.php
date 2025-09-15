<?php

namespace App\Services;

use Microsoft\Graph\GraphServiceClient;
use Microsoft\Graph\Generated\Models\Message;
use Microsoft\Graph\Generated\Models\BodyType;
use Microsoft\Graph\Generated\Models\ItemBody;
use Microsoft\Graph\Generated\Models\Recipient;
use Microsoft\Graph\Generated\Models\EmailAddress;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Graph\Generated\Users\Item\SendMail\SendMailPostRequestBody;
use Microsoft\Graph\Core\Authentication\GraphPhpLeagueAuthenticationProvider;

class GraphMailService
{
    protected GraphServiceClient $graphClient;

    public function __construct()
    {
        $tenantId = env('MSGRAPH_TENANT_ID');
        $clientId = env('MSGRAPH_CLIENT_ID');
        $clientSecret = env('MSGRAPH_CLIENT_SECRET');
        // Callback function untuk akses token dari cache/refresh token
        // Context untuk client credentials
        $tokenRequestContext = new ClientCredentialContext(
            $tenantId,
            $clientId,
            $clientSecret
        );
        // Provider auth yang menggunakan phpleague
        $authProvider = new GraphPhpLeagueAuthenticationProvider($tokenRequestContext);
        $this->graphClient = new GraphServiceClient($authProvider, ['https://graph.microsoft.com/.default']);
    }

    /**
     * Ambil access token dari cache / database / redis
     * (implementasi disesuaikan dengan OAuth flow di aplikasi kamu)
     */
    protected function getAccessToken(): string
    {
        // contoh ambil dari session/redis
        return cache('msgraph_token');
    }

    /**
     * Kirim email via Microsoft Graph
     */
     public function sendMail(string $fromUserId, string $to, string $subject, string $body): void
    {
        // Build message
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
            ->post($sendMailBody)
            ->wait(); // async → tunggu selesai
    }
}
