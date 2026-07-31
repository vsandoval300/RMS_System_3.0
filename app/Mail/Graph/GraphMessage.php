<?php

namespace App\Mail\Graph;

class GraphMessage
{
    public array $to = [];

    public array $cc = [];

    public array $bcc = [];

    public array $replyTo = [];

    public array $attachments = [];

    public string $subject = '';

    public ?string $html = null;

    public ?string $text = null;

    public bool $saveToSentItems = true;

    public function addTo(string $email, ?string $name = null): self
    {
        $this->to[] = compact('email', 'name');

        return $this;
    }

    public function addCc(string $email, ?string $name = null): self
    {
        $this->cc[] = compact('email', 'name');

        return $this;
    }

    public function addBcc(string $email, ?string $name = null): self
    {
        $this->bcc[] = compact('email', 'name');

        return $this;
    }

    public function addReplyTo(string $email, ?string $name = null): self
    {
        $this->replyTo[] = compact('email', 'name');

        return $this;
    }

    public function toArray(): array
    {
        return [
            'subject' => $this->subject,

            'body' => [
                'contentType' => $this->html ? 'HTML' : 'Text',
                'content' => $this->html ?? $this->text ?? '',
            ],

            'toRecipients' => $this->mapAddresses($this->to),

            'ccRecipients' => $this->mapAddresses($this->cc),

            'bccRecipients' => $this->mapAddresses($this->bcc),

            'replyTo' => $this->mapAddresses($this->replyTo),

            'attachments' => collect($this->attachments)
                ->map(fn ($attachment) => $attachment->toArray())
                ->values()
                ->all(),
        ];
    }

    private function mapAddresses(array $addresses): array
    {
        return collect($addresses)
            ->map(fn ($address) => [
                'emailAddress' => array_filter([
                    'address' => $address['email'],
                    'name' => $address['name'],
                ]),
            ])
            ->values()
            ->all();
    }
}