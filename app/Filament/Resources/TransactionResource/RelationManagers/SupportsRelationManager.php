<?php

namespace App\Filament\Resources\TransactionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;           // 👈 importa la facade
use Illuminate\Support\Str;

class SupportsRelationManager extends RelationManager
{
    protected static string $relationship = 'supports';
    protected static ?string $title = 'Transaction Supports';

    public function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('description')
                ->label('Description')
                ->required()
                ->rows(3)
                ->columnSpanFull(),

            FileUpload::make('support_path')
                ->label('Support file')
                ->disk('s3')
                ->directory('reinsurers/transactions/general_support')
                ->visibility('public')
                ->preserveFilenames()
                ->openable()
                ->downloadable()
                // Si de verdad quieres "cualquier tipo", NO pongas acceptedFileTypes
                ->maxSize(51200)
                ->helperText('You can upload any type of file (PDF, images, Excel, Word, ZIP, etc.).')
                ->columnSpanFull(),
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo

                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->searchable(),

                // 👉 Nombre del archivo (solo texto)
                TextColumn::make('support_path')
                    ->label('File')
                    ->formatStateUsing(fn ($state) => $state ? basename($state) : '—')
                    ->icon(fn ($state, $record) =>
                        filled($record->support_path) ? 'heroicon-o-document-text' : 'heroicon-o-x-circle'
                    )
                    ->color(fn ($state, $record) =>
                        filled($record->support_path) ? 'primary' : 'danger'
                    )
                    ->tooltip(fn ($state, $record) =>
                        filled($record->support_path) ? 'View file' : 'No document available'
                    )
                    ->extraAttributes(['class' => 'cursor-pointer'])
                    ->searchable()
                    ->sortable()
                    ->action(
                        Action::make('viewFile')
                            ->label('View file')
                            ->hidden(fn ($record) => blank($record->support_path))
                            ->modalHeading(function ($record) {
                                $name = basename($record->support_path);
                                return "File – {$name}";
                            })
                            ->modalWidth('7xl')
                            ->modalSubmitAction(false)
                            ->modalContent(function ($record) {
                                $path = $record->support_path;

                                if (blank($path)) {
                                    return new HtmlString('<p>No document available.</p>');
                                }

                                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                                $disk = Storage::disk('s3');

                                // Normaliza key (si viene URL completa)
                                if (filter_var($path, FILTER_VALIDATE_URL)) {
                                    $parsed = parse_url($path);
                                    $key = ltrim($parsed['path'] ?? '', '/');
                                } else {
                                    $key = $path;
                                }

                                if (! $disk->exists($key)) {
                                    return new HtmlString(
                                        '<p>The file does not exist in S3.</p>'
                                        .'<p><code>' . e($key) . '</code></p>'
                                    );
                                }

                                $ext = Str::lower(pathinfo($key, PATHINFO_EXTENSION));
                                $filename = basename($key);

                                // Helper: generar URL temporal "inline"
                                $tempUrl = function (?string $contentType = null) use ($disk, $key, $filename) {
                                    $headers = [
                                        'ResponseContentDisposition' => 'inline; filename="' . $filename . '"',
                                    ];

                                    if ($contentType) {
                                        $headers['ResponseContentType'] = $contentType;
                                    }

                                    return $disk->temporaryUrl($key, now()->addMinutes(10), $headers);
                                };

                                // 1) PDF
                                if ($ext === 'pdf') {
                                    $url = $tempUrl('application/pdf');

                                    return view('filament.components.pdf-viewer', [
                                        'url' => $url,
                                    ]);
                                }

                                // 2) Imágenes
                                $imageExts = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
                                if (in_array($ext, $imageExts, true)) {
                                    $mime = match ($ext) {
                                        'png'  => 'image/png',
                                        'jpg', 'jpeg' => 'image/jpeg',
                                        'webp' => 'image/webp',
                                        'gif'  => 'image/gif',
                                        default => null,
                                    };

                                    $url = $tempUrl($mime);

                                    return new HtmlString(
                                        '<div class="w-full flex justify-center">'
                                        .'<img src="'.e($url).'" alt="'.e($filename).'" class="max-w-full h-auto rounded-lg shadow" />'
                                        .'</div>'
                                    );
                                }

                                // 3) Office docs (Excel / Word / PPT) con Office Online Viewer
                                $officeExts = ['xlsx', 'xls', 'docx', 'doc', 'pptx', 'ppt'];
                                if (in_array($ext, $officeExts, true)) {
                                    // Aquí NO siempre conviene forzar ResponseContentType, a veces Office viewer es sensible.
                                    $url = $disk->temporaryUrl($key, now()->addMinutes(10), [
                                        'ResponseContentDisposition' => 'inline; filename="' . $filename . '"',
                                    ]);

                                    $officeViewer = 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($url);

                                    return new HtmlString(
                                        '<div class="w-full" style="height: 75vh;">'
                                        .'<iframe src="'.e($officeViewer).'" style="width:100%; height:100%; border:0;" allowfullscreen></iframe>'
                                        .'</div>'
                                        .'<p class="text-sm text-gray-500 mt-2">'
                                        .'If the preview doesn’t load, use the download button.'
                                        .'</p>'
                                    );
                                }

                                // 4) Otros tipos: fallback a descarga
                                $downloadUrl = $disk->temporaryUrl($key, now()->addMinutes(10));

                                return new HtmlString(
                                    '<p>This file type can’t be previewed here.</p>'
                                    .'<p><a class="text-primary-600 underline" href="'.e($downloadUrl).'" target="_blank" rel="noopener noreferrer">'
                                    .'Download file</a></p>'
                                );
                            })
                    ),
            ])
            //->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add support'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'asc');
    }
}
