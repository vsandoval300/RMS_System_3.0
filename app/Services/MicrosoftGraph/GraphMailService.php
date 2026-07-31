<?php

namespace App\Services\MicrosoftGraph;

use App\Mail\Graph\MicrosoftGraph\GraphMessage;
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
                'message' => $message,
                'saveToSentItems' => true,
            ]
        )
        ->throw();
    }

    protected function mapRecipients(string|array $emails): array
    {
        $emails = is_array($emails)
            ? $emails
            : [$emails];

        return collect($emails)
            ->map(fn ($email) => [
                'emailAddress' => [
                    'address' => $email,
                ],
            ])
            ->values()
            ->all();
    }
}