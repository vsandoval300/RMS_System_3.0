<?php

namespace App\Mail\Graph;

class GraphAttachment
{
    public function __construct(
        public readonly string $name,
        public readonly string $mime,
        public readonly string $content
    ) {
    }

    public function toArray(): array
    {
        return [

            '@odata.type' => '#microsoft.graph.fileAttachment',

            'name' => $this->name,

            'contentType' => $this->mime,

            'contentBytes' => base64_encode($this->content),
        ];
    }
}