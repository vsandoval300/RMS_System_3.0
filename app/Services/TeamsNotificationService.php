<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TeamsNotificationService
{
    public function sendToUser(
        string $recipient,
        array $body
    ): void {
        Http::timeout(10)
            ->connectTimeout(5)
            ->post(
                config('services.teams.webhook'),
                [
                    'type' => 'message',
                    'attachments' => [
                        [
                            'contentType' => 'application/vnd.microsoft.card.adaptive',
                            'content' => [
                                '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
                                'type' => 'AdaptiveCard',
                                'version' => '1.4',

                                // Power Automate lo utiliza para
                                // determinar el destinatario.
                                'recipient' => $recipient,

                                'body' => $body,
                            ],
                        ],
                    ],
                ]
            )
            ->throw();
    }

    public function businessSubmitted(
        string $recipient,
        string $businessCode,
        string $description,
        string $submitterName,
        string $reviewUrl
    ): void {
        $this->sendToUser(
            recipient: $recipient,
            body: [
                [
                    'type' => 'TextBlock',
                    'text' => '📋 Business Submitted for Review',
                    'weight' => 'Bolder',
                    'size' => 'Medium',
                    'wrap' => true,
                ],
                [
                    'type' => 'FactSet',
                    'facts' => [
                        [
                            'title' => 'Business',
                            'value' => $businessCode,
                        ],
                        [
                            'title' => 'Description',
                            'value' => $description,
                        ],
                        [
                            'title' => 'Submitted By',
                            'value' => $submitterName,
                        ],
                    ],
                ],
                [
                    'type' => 'TextBlock',
                    'text' => 'A new Business is awaiting your review.',
                    'wrap' => true,
                ],
                [
                    'type' => 'ActionSet',
                    'actions' => [
                        [
                            'type' => 'Action.OpenUrl',
                            'title' => 'Review Business',
                            'url' => $reviewUrl,
                        ],
                    ],
                ],
            ]
        );
    }
}