<?php

namespace App\Services\MicrosoftGraph;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GraphTokenService
{
    public function getAccessToken(): string
    {
        return Cache::remember('graph_access_token', now()->addMinutes(55), function () {

            $response = Http::asForm()->post(
                sprintf(
                    'https://login.microsoftonline.com/%s/oauth2/v2.0/token',
                    config('services.graph.tenant')
                ),
                [
                    'client_id'     => config('services.graph.client_id'),
                    'client_secret' => config('services.graph.client_secret'),
                    'grant_type'    => 'client_credentials',
                    'scope'         => 'https://graph.microsoft.com/.default',
                ]
            );

            $response->throw();

            return $response->json('access_token');
        });
    }

    public function forgetToken(): void
    {
        Cache::forget('graph_access_token');
    }
}