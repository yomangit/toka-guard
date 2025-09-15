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

class GraphMailService
{
    protected $graph;

    public function __construct()
    {
        $tenantId = config('services.msgraph.tenant_id');
        $clientId = config('services.msgraph.client_id');
        $clientSecret = config('services.msgraph.client_secret');

        // Context client credentials (v2.47 cara resmi)
        $tokenRequestContext = new ClientCredentialContext(
            $tenantId,
            $clientId,
            $clientSecret
        );
        $this->graph = new GraphServiceClient($tokenRequestContext);
    }
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
