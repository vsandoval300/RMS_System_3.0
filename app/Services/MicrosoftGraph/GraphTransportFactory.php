<?php

namespace App\Services\MicrosoftGraph;

use App\Mail\Graph\GraphMessageFactory;
use App\Mail\Graph\GraphTransport;
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