<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Facades\Http;

class TeamsNotificationService
{
    // Http::post($url, [
    //     "type" => "message",
    //     "attachments" => [
    //         [
    //             "contentType" => "application/vnd.microsoft.card.adaptive",
    //             "content" => [
    //                 "\$schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
    //                 "type" => "AdaptiveCard",
    //                 "version" => "1.4",
    //                 "body" => [
    //                     [
    //                         "type" => "TextBlock",
    //                         "text" => "Prueba desde Laravel"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ]);
    /* public function businessSubmitted(
        Business $business,
        string $submitterName,
    ): void {

        $payload = [
            'type' => 'message',
            'attachments' => [
                [
                    'contentType' => 'application/vnd.microsoft.card.adaptive',
                    'contentUrl' => null,
                    'content' => [
                        '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
                        'type' => 'AdaptiveCard',
                        'version' => '1.5',

                        'body' => [

                            [
                                'type' => 'TextBlock',
                                'text' => '📋 Business Submitted for Review',
                                'weight' => 'Bolder',
                                'size' => 'Large',
                            ],

                            [
                                'type' => 'FactSet',
                                'facts' => [
                                    [
                                        'title' => 'Business',
                                        'value' => $business->business_code,
                                    ],
                                    [
                                        'title' => 'Description',
                                        'value' => $business->description,
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
                                'spacing' => 'Medium',
                            ],
                        ],

                        'actions' => [

                            [
                                'type' => 'Action.OpenUrl',
                                'title' => 'Review Business',
                                'url' => route(
                                    'filament.admin.resources.businesses.edit',
                                    $business
                                ),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        Http::timeout(15)
            ->post(config('services.teams.webhook'), $payload)
            ->throw();
    } */
}