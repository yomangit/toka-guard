<?php

namespace App\Services;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class GraphMailService
{
    protected $graph;

    public function __construct()
    {
        $token = $this->getAccessToken();
        
        $this->graph = new Graph();
        $this->graph->setAccessToken($token);
    }

    protected function getAccessToken()
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

    public function sendMail($from, $to, $subject, $body)
    {
        $message = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $body,
                ],
                'from' => [
                    'emailAddress' => [
                        'address' => $from,
                    ],
                ],
                'toRecipients' => [
                    [
                        'emailAddress' => [
                            'address' => $to,
                        ],
                    ],
                ],
            ],
            'saveToSentItems' => 'true',
        ];

        return $this->graph->createRequest("POST", "/users/{$from}/sendMail")
            ->attachBody($message)
            ->execute();
    }
}
