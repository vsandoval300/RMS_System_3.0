<?php

namespace App\Filament\Resources\Reinsurers\Pages;

use App\Filament\Resources\Reinsurers\ReinsurersResource;
use Filament\Resources\Pages\Page;

class ReinsurerOverview extends Page
{
    protected static string $resource = ReinsurersResource::class;

    protected string $view = 'filament.resources.reinsurers-resource.pages.reinsurer-overview';
}
