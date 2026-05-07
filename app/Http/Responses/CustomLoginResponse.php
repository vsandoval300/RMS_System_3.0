<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request)
    {
        // (Opcional) limpia el intended viejo
        $request->session()->forget('url.intended');

        // Redirige siempre al dashboard del panel actual
        return redirect()->to(Filament::getCurrentOrDefaultPanel()->getUrl());
    }
}