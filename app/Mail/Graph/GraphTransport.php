<?php

namespace App\Mail\Graph;

use App\Services\MicrosoftGraph\GraphMailService;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

class GraphTransport extends AbstractTransport
{
    public function __construct(
        protected GraphMailService $mailer,
        protected GraphMessageFactory $factory,
    ) {
        parent::__construct();
    }

    protected function doSend(
        SentMessage $message
    ): void {

        /** @var Email $email */
        $email = $message->getOriginalMessage();

        $graphMessage = $this->factory->create($email);

        $this->mailer->send($graphMessage);
    }

    public function __toString(): string
    {
        return 'graph';
    }
}