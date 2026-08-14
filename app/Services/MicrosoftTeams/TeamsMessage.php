<?php

namespace App\Services\MicrosoftTeams;

class TeamsMessage
{
    protected array $body = [];

    protected array $actions = [];

    public static function make(): self
    {
        return new self();
    }

    public function title(string $title): self
    {
        $this->body[] = [
            'type' => 'TextBlock',
            'text' => $title,
            'weight' => 'Bolder',
            'size' => 'Medium',
            'wrap' => true,
        ];

        return $this;
    }

    public function text(string $text): self
    {
        $this->body[] = [
            'type' => 'TextBlock',
            'text' => $text,
            'wrap' => true,
        ];

        return $this;
    }

    public function action(string $title, string $url): self
    {
        $this->actions[] = [
            'type' => 'Action.OpenUrl',
            'title' => $title,
            'url' => $url,
        ];

        return $this;
    }

    public function toArray(): array
    {
        $card = [
            '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
            'type' => 'AdaptiveCard',
            'version' => '1.4',
            'body' => $this->body,
        ];

        if ($this->actions !== []) {
            $card['actions'] = $this->actions;
        }

        return $card;
    }
}


