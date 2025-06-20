<?php

namespace App\Filament\Resources\ReinsurersResource\Pages;

use App\Filament\Resources\ReinsurersResource;
use Filament\Resources\Pages\Page;

class ReinsurerOverview extends Page
{
    protected static string $resource = ReinsurersResource::class;

    protected static string $view = 'filament.resources.reinsurers-resource.pages.reinsurer-overview';
}
