<?php

namespace App\Mail\Graph;

use App\Services\MicrosoftGraph\GraphMailService;

class GraphTransportFactory
{
    public function __construct(
        protected GraphMailService $mailer,
        protected GraphMessageFactory $factory,
    ) {
    }

    public function create(): GraphTransport
    {
        return new GraphTransport(
            $this->mailer,
            $this->factory,
        );
    }
}