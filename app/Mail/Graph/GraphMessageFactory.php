<?php

namespace App\Mail\Graph;

use App\Mail\Graph\GraphAttachment as GraphGraphAttachment;
use App\Mail\Graph\GraphMessage;
use App\Mail\Graph\GraphAttachment;
use Symfony\Component\Mime\Email;

class GraphMessageFactory
{
    public function create(Email $email): GraphMessage
    {
        $graph = new GraphMessage();

        $graph->subject = $email->getSubject() ?? '';

        $graph->html = $email->getHtmlBody();

        $graph->text = $email->getTextBody();

        foreach ($email->getTo() as $recipient) {
            $graph->addTo(
                $recipient->getAddress(),
                $recipient->getName()
            );
        }

        foreach ($email->getCc() as $recipient) {
            $graph->addCc(
                $recipient->getAddress(),
                $recipient->getName()
            );
        }

        foreach ($email->getBcc() as $recipient) {
            $graph->addBcc(
                $recipient->getAddress(),
                $recipient->getName()
            );
        }

        foreach ($email->getReplyTo() as $recipient) {
            $graph->addReplyTo(
                $recipient->getAddress(),
                $recipient->getName()
            );
        }

        foreach ($email->getAttachments() as $attachment) {

            $graph->attachments[] = new GraphGraphAttachment(
                name: $this->attachmentName($attachment),
                mime: $this->attachmentMime($attachment),
                content: $attachment->bodyToString(),
            );
        }

        return $graph;
    }

    protected function attachmentName($attachment): string
    {
        return $attachment
            ->getPreparedHeaders()
            ->getHeaderParameter(
                'Content-Disposition',
                'filename'
            ) ?? 'attachment';
    }

    protected function attachmentMime($attachment): string
    {
        return $attachment->getMediaType()
            . '/'
            . $attachment->getMediaSubtype();
    }
}