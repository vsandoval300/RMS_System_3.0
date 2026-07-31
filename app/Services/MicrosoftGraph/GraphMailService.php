<?php

namespace App\Services\MicrosoftGraph;

use App\Mail\Graph\GraphMessage;
use Illuminate\Support\Facades\Http;

class GraphMailService
{
    public function __construct(
        protected GraphTokenService $tokenService
    ) {
    }

    public function send(GraphMessage $message): void
    {
        Http::withToken(
            $this->tokenService->getAccessToken()
        )
        ->post(
            sprintf(
                'https://graph.microsoft.com/v1.0/users/%s/sendMail',
                config('services.graph.from')
            ),
            [
                'message' => $message->toArray(),
                'saveToSentItems' => $message->saveToSentItems,
            ]
        )
        ->throw();
    }
}