<?php

namespace App\Filament\Resources\Businesses\Pages;

use App\Exports\BusinessTemplateExport;
use App\Filament\Resources\Businesses\BusinessResource;
use App\Models\Business;
use App\Models\Currency;
use App\Models\Partner;
use App\Models\Region;
use App\Models\Reinsurer;
use App\Models\Treaty;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportBusinesses extends Page
{
    use WithFileUploads;

    protected static string $resource = BusinessResource::class;
    protected string $view = 'filament.resources.businesses.import-businesses';

    // ── Enum constraints ───────────────────────────────────────────────────────
    private const REINSURANCE_TYPES = ['Facultative', 'Treaty'];
    private const RISK_COVERED      = ['Life', 'Non-Life'];
    private const BUSINESS_TYPES    = ['Own', 'Third party'];
    private const PREMIUM_TYPES     = ['Fixed', 'Estimated', 'Declared'];
    private const PURPOSES          = ['Strategic', 'Traditional'];
    private const CLAIMS_TYPES      = ['Claims occurrence', 'Claims made', 'Hybrid'];

    // ── State ──────────────────────────────────────────────────────────────────
    // idle | errors | preview | imported

    public string $state       = 'idle';
    public mixed  $importFile  = null;

    /** @var array<int, array<string,mixed>> */
    public array $previewRows  = [];

    /** @var array<int, array<string,mixed>> */
    public array $errorRows    = [];

    public int $importedCount  = 0;
    public int $insertedCount  = 0;
    public int $updatedCount   = 0;

    // ── Template download ──────────────────────────────────────────────────────

    public function downloadTemplate(): StreamedResponse|BinaryFileResponse
    {
        return Excel::download(
            BusinessTemplateExport::build(),
            'businesses_import_template.xlsx'
        );
    }

    // ── File processing ────────────────────────────────────────────────────────

    public function updatedImportFile(): void
    {
        $this->processFile();
    }

    public function processFile(): void
    {
        if (! $this->importFile) {
            return;
        }

        // ── 1. Build lookup maps (once, keyed for O(1) resolve) ───────────────
        $reinsurers = Reinsurer::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [mb_strtolower(trim($name)) => $id])
            ->toArray();

        $partners = Partner::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [mb_strtolower(trim($name)) => $id])
            ->toArray();

        $currencies = Currency::pluck('id', 'acronym')
            ->mapWithKeys(fn ($id, $acronym) => [strtoupper(trim($acronym)) => $id])
            ->toArray();

        $regions = Region::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [mb_strtolower(trim($name)) => $id])
            ->toArray();

        $treatyCodes = Treaty::pluck('treaty_code')
            ->flip()
            ->toArray(); // treaty_code => true

        $existingBusinessCodes = Business::withTrashed()
            ->pluck('business_code')
            ->flip()
            ->toArray(); // business_code => true

        // ── 2. Read Excel — first sheet only ──────────────────────────────────
        $path = $this->importFile->getRealPath();
        $data = Excel::toArray(null, $path, null, \Maatwebsite\Excel\Excel::XLSX);

        if (empty($data[0])) {
            $this->state     = 'errors';
            $this->errorRows = [['row' => '—', 'business_code' => '—', 'errors' => ['The uploaded file appears to be empty or could not be read.']]];
            return;
        }

        $allRows  = $data[0];
        $dataRows = array_slice($allRows, 1); // skip header row

        $this->previewRows = [];
        $this->errorRows   = [];

        // ── 3. Validate each row ──────────────────────────────────────────────
        foreach ($dataRows as $i => $row) {
            $lineNo = $i + 2; // +1 for header, +1 for 1-based

            // Pad row to 16 columns
            $row = array_pad((array) $row, 16, null);

            $businessCode    = trim((string) ($row[0]  ?? ''));
            $sourceCode      = trim((string) ($row[1]  ?? ''));
            $description     = trim((string) ($row[2]  ?? ''));
            $reinsuranceType = trim((string) ($row[3]  ?? ''));
            $riskCovered     = trim((string) ($row[4]  ?? ''));
            $businessType    = trim((string) ($row[5]  ?? ''));
            $premiumType     = trim((string) ($row[6]  ?? ''));
            $purpose         = trim((string) ($row[7]  ?? ''));
            $claimsType      = trim((string) ($row[8]  ?? ''));
            $reinsurName     = trim((string) ($row[9]  ?? ''));
            $producerName    = trim((string) ($row[10] ?? ''));
            $currencyCode    = strtoupper(trim((string) ($row[11] ?? '')));
            $regionName      = trim((string) ($row[12] ?? ''));
            $treatyCode      = trim((string) ($row[13] ?? ''));
            $renewedFrom     = trim((string) ($row[14] ?? ''));
            $indexRaw        = $row[15];

            // Skip completely empty rows
            if ($businessCode === '' && $description === '' && $reinsurName === '') {
                continue;
            }

            $errors = [];

            // ── Required text fields ──────────────────────────────────────────
            if ($businessCode === '') {
                $errors[] = 'business_code is required.';
            } elseif (strlen($businessCode) > 19) {
                $errors[] = "business_code must be at most 19 characters (got " . strlen($businessCode) . ").";
            }

            if ($description === '') {
                $errors[] = 'description is required.';
            }

            // ── Enum validations ──────────────────────────────────────────────
            if ($reinsuranceType === '') {
                $errors[] = 'reinsurance_type is required.';
            } elseif (! in_array($reinsuranceType, self::REINSURANCE_TYPES, true)) {
                $errors[] = "Invalid reinsurance_type: '{$reinsuranceType}'. Allowed: " . implode(', ', self::REINSURANCE_TYPES) . '.';
            }

            if ($riskCovered === '') {
                $errors[] = 'risk_covered is required.';
            } elseif (! in_array($riskCovered, self::RISK_COVERED, true)) {
                $errors[] = "Invalid risk_covered: '{$riskCovered}'. Allowed: " . implode(', ', self::RISK_COVERED) . '.';
            }

            if ($businessType === '') {
                $errors[] = 'business_type is required.';
            } elseif (! in_array($businessType, self::BUSINESS_TYPES, true)) {
                $errors[] = "Invalid business_type: '{$businessType}'. Allowed: " . implode(', ', self::BUSINESS_TYPES) . '.';
            }

            if ($premiumType === '') {
                $errors[] = 'premium_type is required.';
            } elseif (! in_array($premiumType, self::PREMIUM_TYPES, true)) {
                $errors[] = "Invalid premium_type: '{$premiumType}'. Allowed: " . implode(', ', self::PREMIUM_TYPES) . '.';
            }

            if ($purpose === '') {
                $errors[] = 'purpose is required.';
            } elseif (! in_array($purpose, self::PURPOSES, true)) {
                $errors[] = "Invalid purpose: '{$purpose}'. Allowed: " . implode(', ', self::PURPOSES) . '.';
            }

            if ($claimsType === '') {
                $errors[] = 'claims_type is required.';
            } elseif (! in_array($claimsType, self::CLAIMS_TYPES, true)) {
                $errors[] = "Invalid claims_type: '{$claimsType}'. Allowed: " . implode(', ', self::CLAIMS_TYPES) . '.';
            }

            // ── FK lookups ────────────────────────────────────────────────────
            $reinsurerIdResolved = null;
            if ($reinsurName === '') {
                $errors[] = 'reinsurer_name is required.';
            } else {
                $reinsurerIdResolved = $reinsurers[mb_strtolower($reinsurName)] ?? null;
                if ($reinsurerIdResolved === null) {
                    $errors[] = "Reinsurer not found: '{$reinsurName}'. Check REF_Reinsurers sheet.";
                }
            }

            $producerIdResolved = null;
            if ($producerName === '') {
                $errors[] = 'producer_name is required.';
            } else {
                $producerIdResolved = $partners[mb_strtolower($producerName)] ?? null;
                if ($producerIdResolved === null) {
                    $errors[] = "Producer not found: '{$producerName}'. Check REF_Partners sheet.";
                }
            }

            $currencyIdResolved = null;
            if ($currencyCode === '') {
                $errors[] = 'currency_code is required.';
            } else {
                $currencyIdResolved = $currencies[$currencyCode] ?? null;
                if ($currencyIdResolved === null) {
                    $errors[] = "Currency code not found: '{$currencyCode}'. Check REF_Currencies sheet.";
                }
            }

            $regionIdResolved = null;
            if ($regionName === '') {
                $errors[] = 'region_name is required.';
            } else {
                $regionIdResolved = $regions[mb_strtolower($regionName)] ?? null;
                if ($regionIdResolved === null) {
                    $errors[] = "Region not found: '{$regionName}'. Check REF_Regions sheet.";
                }
            }

            // ── Optional FK: treaty ───────────────────────────────────────────
            $parentIdResolved = null;
            if ($treatyCode !== '') {
                if (! isset($treatyCodes[$treatyCode])) {
                    $errors[] = "Treaty Code not found: '{$treatyCode}'. Check REF_Treaties sheet.";
                } else {
                    $parentIdResolved = $treatyCode;
                }
            }

            // ── Optional FK: renewed_from ─────────────────────────────────────
            $renewedFromResolved = null;
            if ($renewedFrom !== '') {
                if (! isset($existingBusinessCodes[$renewedFrom])) {
                    $errors[] = "Renewed From business_code does not exist: '{$renewedFrom}'.";
                } else {
                    $renewedFromResolved = $renewedFrom;
                }
            }

            // ── Index ─────────────────────────────────────────────────────────
            $index = ($indexRaw !== null && $indexRaw !== '') ? (int) $indexRaw : 1;
            if ($index < 1) {
                $errors[] = "index must be a positive integer (got: {$index}).";
            }

            // ── Determine insert vs update ────────────────────────────────────
            $isUpdate = isset($existingBusinessCodes[$businessCode]);

            $rowData = [
                'row'              => $lineNo,
                'business_code'    => $businessCode,
                'source_code'      => $sourceCode !== '' ? $sourceCode : null,
                'description'      => $description,
                'reinsurance_type' => $reinsuranceType,
                'risk_covered'     => $riskCovered,
                'business_type'    => $businessType,
                'premium_type'     => $premiumType,
                'purpose'          => $purpose,
                'claims_type'      => $claimsType,
                'reinsurer_id'     => $reinsurerIdResolved,
                'producer_id'      => $producerIdResolved,
                'currency_id'      => $currencyIdResolved,
                'region_id'        => $regionIdResolved,
                'parent_id'        => $parentIdResolved,
                'renewed_from_id'  => $renewedFromResolved,
                'index'            => $index,
                '_reinsurer_name'  => $reinsurName,
                '_currency_code'   => $currencyCode,
                '_region_name'     => $regionName,
                '_is_update'       => $isUpdate,
            ];

            if (! empty($errors)) {
                $rowData['errors'] = $errors;
                $this->errorRows[] = $rowData;
            } else {
                $this->previewRows[] = $rowData;
            }
        }

        // ── 4. Decide state ───────────────────────────────────────────────────
        if (! empty($this->errorRows)) {
            $this->state = 'errors';
        } elseif (! empty($this->previewRows)) {
            $this->state = 'preview';
        } else {
            // File was empty (all rows skipped)
            $this->state     = 'errors';
            $this->errorRows = [['row' => '—', 'business_code' => '—', 'errors' => ['No data rows found in the file. Make sure you filled the Businesses sheet.']]];
        }
    }

    // ── Import confirmation ────────────────────────────────────────────────────

    public function confirmImport(): void
    {
        if ($this->state !== 'preview' || empty($this->previewRows)) {
            return;
        }

        $inserted = 0;
        $updated  = 0;

        DB::transaction(function () use (&$inserted, &$updated) {
            foreach ($this->previewRows as $row) {
                $payload = [
                    'source_code'      => $row['source_code'],
                    'index'            => $row['index'],
                    'description'      => $row['description'],
                    'reinsurance_type' => $row['reinsurance_type'],
                    'risk_covered'     => $row['risk_covered'],
                    'business_type'    => $row['business_type'],
                    'premium_type'     => $row['premium_type'],
                    'purpose'          => $row['purpose'],
                    'claims_type'      => $row['claims_type'],
                    'reinsurer_id'     => $row['reinsurer_id'],
                    'producer_id'      => $row['producer_id'],
                    'currency_id'      => $row['currency_id'],
                    'region_id'        => $row['region_id'],
                    'parent_id'        => $row['parent_id'],
                    'renewed_from_id'  => $row['renewed_from_id'],
                ];

                $existing = Business::withTrashed()->find($row['business_code']);

                if ($existing) {
                    // UPDATE — never overwrite approval_status, lifecycle or created_by_user
                    $existing->fill($payload)->save();
                    $updated++;
                } else {
                    // INSERT — defaults apply (approval_status = DFT via DB, lifecycle via model hook)
                    Business::create(array_merge($payload, [
                        'business_code'  => $row['business_code'],
                        'created_by_user' => Auth::id(),
                    ]));
                    $inserted++;
                }
            }
        });

        $this->importedCount = $inserted + $updated;
        $this->insertedCount = $inserted;
        $this->updatedCount  = $updated;
        $this->state         = 'imported';

        Notification::make()
            ->success()
            ->title('Import completed')
            ->body("{$inserted} inserted · {$updated} updated")
            ->send();
    }

    // ── Reset ──────────────────────────────────────────────────────────────────

    public function resetState(): void
    {
        $this->state        = 'idle';
        $this->importFile   = null;
        $this->previewRows  = [];
        $this->errorRows    = [];
        $this->importedCount = 0;
        $this->insertedCount = 0;
        $this->updatedCount  = 0;
    }

    // ── Page meta ──────────────────────────────────────────────────────────────

    public function getTitle(): string
    {
        return 'Import Businesses';
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user instanceof \App\Models\User && $user->can('create_business');
    }
}
