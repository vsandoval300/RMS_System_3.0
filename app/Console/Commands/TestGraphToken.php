<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MicrosoftGraph\GraphTokenService;

class TestGraphToken extends Command
{
    protected $signature = 'graph:test-token';

    protected $description = 'Prueba autenticación con Microsoft Graph';

    public function handle(GraphTokenService $graph)
    {
        $token = $graph->getAccessToken();

        $this->info('Token obtenido correctamente.');

        $this->line(substr($token, 0, 60) . '...');
    }
}