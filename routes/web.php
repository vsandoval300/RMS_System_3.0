<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\OperativeDoc;

// Redirige raíz a Filament
Route::redirect('/', '/admin');

// Fallback route named "login" — required because Laravel's default `auth`
// middleware redirects unauthenticated users to route('login'), but this app
// only defines Filament's panel-scoped login (admin/login). Without this,
// any plain route using `->middleware('auth')` throws a 500
// (RouteNotFoundException) instead of redirecting when the session isn't
// authenticated.
Route::get('/login', fn () => redirect('/admin/login'))->name('login');

// === New Business Workflow — User Guide manual ===
Route::get('/admin/manual/new-business-workflow', function () {
    return response()->file(base_path('docs/manual/new-business-workflow.html'));
})->name('manual.new-business-workflow')->middleware(['web', 'auth']);

// === Placement Schemes — User Guide manual ===
Route::get('/admin/manual/placement-schemes', function () {
    return response()->file(base_path('docs/manual/placement-schemes-workflow.html'));
})->name('manual.placement-schemes')->middleware(['web', 'auth']);

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

Route::get('/reports/download/{file}', function ($file) {
    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
    $disk = Storage::disk('s3');
    return $disk->download('uw-reports/' . $file);
});

// === Technical Result PDF for Business ===
Route::get('/admin/business/{businessCode}/technical-result.pdf', function (string $businessCode) {

    $business = \App\Models\Business::withoutTrashed()->findOrFail($businessCode);

    $data = app(\App\Services\BusinessTechnicalResultService::class)->build($business);

    $html = view('filament.resources.business.technical-result-pdf', $data)->render();

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', false);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $pdf = new \Dompdf\Dompdf($options);
    $pdf->loadHtml($html);
    $pdf->setPaper('letter', 'portrait');
    $pdf->render();

    $filename = 'business-summary-' . $businessCode . '.pdf';

    return response()->streamDownload(
        fn () => print($pdf->output()),
        $filename,
        ['Content-Type' => 'application/pdf']
    );
})->name('business.technical-result.pdf')->middleware(['web', 'auth']);

// === Operative Document Overview PDF ===
Route::get('/admin/operative-doc/{docId}/overview.pdf', function (string $docId) {

    \App\Models\OperativeDoc::withoutTrashed()->findOrFail($docId);

    $data = app(\App\Services\OperativeDocSummaryV2Service::class)->build($docId);

    $html = view('filament.resources.business.operative-doc-summary-pdf', $data)->render();

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', false);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $pdf = new \Dompdf\Dompdf($options);
    $pdf->loadHtml($html);
    $pdf->setPaper('A4', 'portrait');
    $pdf->render();

    $filename = 'operative-doc-' . $docId . '.pdf';

    return response()->streamDownload(
        fn () => print($pdf->output()),
        $filename,
        ['Content-Type' => 'application/pdf']
    );
})->name('operative-doc.overview.pdf')->middleware(['web', 'auth']);
