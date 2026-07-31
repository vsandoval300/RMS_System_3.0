<?php

namespace App\Jobs;

use App\Exports\MissingPdfsReportExport;
use App\Models\OperativeDoc;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class GenerateMissingPdfsReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;
    public $tries   = 3;

    public function __construct(
        protected ?string $dateFrom,
        protected ?string $dateTo,
        protected array   $reinsurerIds,
        protected ?int    $userId,
        protected string  $filename,
        protected int     $requestedBy,
    ) {}

    public function handle(): void
    {
        $rows = OperativeDoc::query()
            ->whereNull('operative_docs.document_path')
            ->whereNull('operative_docs.deleted_at')
            ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
            ->whereNull('businesses.deleted_at')
            ->leftJoin('users', 'users.id', '=', 'businesses.created_by_user')
            ->leftJoin('reinsurers', 'reinsurers.id', '=', 'businesses.reinsurer_id')
            ->leftJoin('business_doc_types', 'business_doc_types.id', '=', 'operative_docs.operative_doc_type_id')
            ->when($this->dateFrom, fn ($q) =>
                $q->where('operative_docs.created_at', '>=', $this->dateFrom)
            )
            ->when($this->dateTo, fn ($q) =>
                $q->where('operative_docs.created_at', '<=', $this->dateTo)
            )
            ->when(count($this->reinsurerIds) > 0, fn ($q) =>
                $q->whereIn('businesses.reinsurer_id', $this->reinsurerIds)
            )
            ->when($this->userId, fn ($q) =>
                $q->where('businesses.created_by_user', $this->userId)
            )
            ->orderBy('businesses.business_code')
            ->orderBy('operative_docs.id')
            ->select([
                'operative_docs.id as doc_id',
                'operative_docs.description as doc_description',
                'operative_docs.inception_date',
                'operative_docs.expiration_date',
                'operative_docs.created_at as doc_created_at',
                'businesses.business_code',
                'businesses.description as business_description',
                'businesses.created_at as business_created_at',
                'reinsurers.name as reinsurer_name',
                'users.name as created_by_name',
                'business_doc_types.name as doc_type_name',
            ])
            ->get();

        Excel::store(
            new MissingPdfsReportExport($rows),
            'uw-reports/' . $this->filename
        );

        NotifyReportReady::dispatch($this->requestedBy, $this->filename);
    }
}
