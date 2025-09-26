<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // (Opcional) limpia el intended viejo
        $request->session()->forget('url.intended');

        // Redirige siempre al dashboard del panel actual
        return redirect()->to(Filament::getCurrentPanel()->getUrl());
    }
}