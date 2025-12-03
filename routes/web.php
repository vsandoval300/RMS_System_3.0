<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\OperativeDoc;

// Redirige raíz a Filament
Route::redirect('/', '/admin');

// === Visor de PDFs para OperativeDocs ===
Route::get('/pdf-viewer/{operativeDoc}', function (OperativeDoc $operativeDoc) {
    // 1) Tomamos el path y limpiamos espacios
    $path = trim($operativeDoc->document_path ?? '');

    if ($path === '') {
        abort(404, 'No document_path on this record.');
    }

    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
    $disk = Storage::disk('s3');

    // 2) Si viene como URL completa, nos quedamos SOLO con la parte del path
    //    https://rmk-resources.s3.amazonaws.com/reinsurers/business_documents/2016-...pdf
    //    → reinsurers/business_documents/2016-...pdf
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        $urlPath = parse_url($path, PHP_URL_PATH); // "/reinsurers/business_documents/2016-....pdf"
        $path    = ltrim($urlPath, '/');           // "reinsurers/business_documents/2016-....pdf"
    }

    // 3) Comprobamos si la key existe tal cual en S3
    if (! $disk->exists($path)) {
        // Respuesta clara para depurar
        return response(
            "File not found in S3.\nKey used: {$path}",
            404,
            ['Content-Type' => 'text/plain']
        );
    }

    // 4) Leemos el contenido y lo devolvemos inline
    $content = $disk->get($path);

    return response($content, 200, [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
    ]);
})->name('pdf.viewer');
