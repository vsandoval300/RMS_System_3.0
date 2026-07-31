<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MicrosoftGraph\GraphMailService;

class TestGraphMail extends Command
{
    protected $signature = 'graph:test-mail';

    protected $description = 'Enviar correo usando Microsoft Graph';

    public function handle(GraphMailService $graph)
    {
        $graph->send(
            to: 'fls@rainmakergroup.com',
            subject: 'Prueba Microsoft Graph',
            html: '
                <h1>¡Funcionó!</h1>

                <p>Este correo fue enviado desde Laravel usando Microsoft Graph.</p>

                <strong>Ya no estamos usando SMTP.</strong>
            '
        );

        $this->info('Correo enviado.');
    }
}