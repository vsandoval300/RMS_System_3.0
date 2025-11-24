<?php

use Illuminate\Support\Facades\Route;

/* Route::get('/', function () {
    return view('welcome');
}); */

// ✅ redirige raíz a Filament (autenticará y mandará a /admin/login si hace falta)
Route::redirect('/', '/admin');

/* Route::get('/ping', function () {
    return 'pong';
}); */