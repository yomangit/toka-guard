<?php

namespace App\Services;

use Microsoft\Graph\GraphServiceClient;
use Microsoft\Graph\Generated\Models\Message;
use Microsoft\Graph\Generated\Models\BodyType;
use Microsoft\Graph\Generated\Models\ItemBody;
use Microsoft\Graph\Generated\Models\Recipient;
use Microsoft\Graph\Generated\Models\EmailAddress;
use Microsoft\Graph\Generated\Users\Item\SendMail\SendMailPostRequestBody;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Graph\Core\Authentication\GraphPhpLeagueAuthenticationProvider;

class GraphMailService
{
    protected GraphServiceClient $graphClient;

    public function __construct()
    {
        $tenantId     = env('MSGRAPH_TENANT_ID');
        $clientId     = env('MSGRAPH_CLIENT_ID');
        $clientSecret = env('MSGRAPH_CLIENT_SECRET');

        // Context untuk client credentials flow
        $tokenRequestContext = new ClientCredentialContext(
            $tenantId,
            $clientId,
            $clientSecret
        );

        // Auth provider pakai league/oauth2-client
        $authProvider = new GraphPhpLeagueAuthenticationProvider($tokenRequestContext);

        // Graph Service Client
        $this->graphClient = new GraphServiceClient($authProvider);
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

        // Send email (async → tunggu selesai)
        $this->graphClient
            ->users()
            ->byUserId($fromUserId)
            ->sendMail()
            ->post($sendMailBody)
            ->wait();
    }
}
