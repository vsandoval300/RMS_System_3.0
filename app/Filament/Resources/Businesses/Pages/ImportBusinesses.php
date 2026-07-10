<?php

namespace App\Filament\Resources\Businesses\Pages;

use App\Exports\BusinessTemplateExport;
use App\Exports\CostSchemeTemplateExport;
use App\Exports\LiabilityStructureTemplateExport;
use App\Exports\DocSchemeTemplateExport;
use App\Exports\InsuredTemplateExport;
use App\Exports\OperativeDocTemplateExport;
use App\Filament\Resources\Businesses\BusinessResource;
use App\Models\Business;
use App\Models\BusinessDocType;
use App\Models\BusinessOpDocsInsured;
use App\Models\BusinessOpDocsScheme;
use App\Models\Company;
use App\Models\CostNodex;
use App\Models\CostScheme;
use App\Models\Coverage;
use App\Models\Currency;
use App\Models\Deduction;
use App\Models\LiabilityStructure;
use App\Models\OperativeDoc;
use App\Models\Partner;
use App\Models\Region;
use App\Models\Reinsurer;
use App\Models\Treaty;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportBusinesses extends Page
{
    use WithFileUploads;

    protected static string $resource = BusinessResource::class;
    protected string $view = 'filament.resources.businesses.import-businesses';

    // ── Enum constraints — Businesses ──────────────────────────────────────────
    private const REINSURANCE_TYPES = ['Facultative', 'Treaty'];
    private const RISK_COVERED      = ['Life', 'Non-Life'];
    private const BUSINESS_TYPES    = ['Own', 'Third party'];
    private const PREMIUM_TYPES     = ['Fixed', 'Estimated', 'Declared'];
    private const PURPOSES          = ['Strategic', 'Traditional'];
    private const CLAIMS_TYPES      = ['Claims occurrence', 'Claims made', 'Hybrid'];

    // ── Enum constraints — Cost Schemes ────────────────────────────────────────
    private const AGREEMENT_TYPES   = ['Quota Share', 'Surplus', 'Excess of Loss', 'Stop Loss'];

    // ── Step 1 state ───────────────────────────────────────────────────────────
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

    // ── Step 3 state ───────────────────────────────────────────────────────────
    // idle | errors | preview | imported
    public string $lsState      = 'idle';
    public mixed  $lsImportFile = null;

    /** @var array<int, array<string,mixed>> */
    public array $lsPreviewRows = [];

    /** @var array<int, array<string,mixed>> */
    public array $lsErrorRows   = [];

    public int $lsInsertedCount = 0;

    // ── Step 4 state ───────────────────────────────────────────────────────────
    // idle | errors | preview | imported
    public string $odState       = 'idle';
    public mixed  $odImportFile  = null;

    /** @var array<int, array<string,mixed>> */
    public array $odPreviewRows  = [];

    /** @var array<int, array<string,mixed>> */
    public array $odErrorRows    = [];

    public int $odInsertedCount  = 0;
    public int $odUpdatedCount   = 0;

    // ── Step 5 state ───────────────────────────────────────────────────────────
    // idle | errors | preview | imported
    public string $biState       = 'idle';
    public mixed  $biImportFile  = null;

    /** @var array<int, array<string,mixed>> */
    public array $biPreviewRows  = [];

    /** @var array<int, array<string,mixed>> */
    public array $biErrorRows    = [];

    public int $biInsertedCount  = 0;

    // ── Step 6 state ───────────────────────────────────────────────────────────
    // idle | errors | preview | imported
    public string $dsState       = 'idle';
    public mixed  $dsImportFile  = null;

    /** @var array<int, array<string,mixed>> */
    public array $dsPreviewRows  = [];

    /** @var array<int, array<string,mixed>> */
    public array $dsErrorRows    = [];

    public int $dsInsertedCount  = 0;

    // ── Step 2 state ───────────────────────────────────────────────────────────
    // idle | errors | preview | imported
    public string $csState     = 'idle';
    public mixed  $csImportFile = null;

    /** @var array<int, array<string,mixed>> */
    public array $csPreviewSchemes = [];

    /** @var array<int, array<string,mixed>> */
    public array $csPreviewNodes   = [];

    /** @var array<int, array<string,mixed>> */
    public array $csErrorRows      = [];

    public int $csImportedSchemes  = 0;
    public int $csInsertedSchemes  = 0;
    public int $csUpdatedSchemes   = 0;
    public int $csImportedNodes    = 0;
    public int $csInsertedNodes    = 0;
    public int $csUpdatedNodes     = 0;

    // ══════════════════════════════════════════════════════════════════════════
    // STEP 1 — Businesses
    // ══════════════════════════════════════════════════════════════════════════

    public function downloadTemplate(): StreamedResponse|BinaryFileResponse
    {
        return Excel::download(
            BusinessTemplateExport::build(),
            'step1_businesses_import_template.xlsx'
        );
    }

    public function updatedImportFile(): void
    {
        $this->processFile();
    }

    public function processFile(): void
    {
        if (! $this->importFile) {
            return;
        }

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
            ->toArray();

        $existingBusinessCodes = Business::withTrashed()
            ->pluck('business_code')
            ->flip()
            ->toArray();

        $path = $this->importFile->getRealPath();
        $data = Excel::toArray(null, $path, null, \Maatwebsite\Excel\Excel::XLSX);

        if (empty($data[0])) {
            $this->state     = 'errors';
            $this->errorRows = [['row' => '—', 'business_code' => '—', 'errors' => ['The uploaded file appears to be empty or could not be read.']]];
            return;
        }

        $allRows  = $data[0];
        $dataRows = array_slice($allRows, 1);

        $this->previewRows = [];
        $this->errorRows   = [];

        foreach ($dataRows as $i => $row) {
            $lineNo = $i + 2;
            $row    = array_pad((array) $row, 16, null);

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

            if ($businessCode === '' && $description === '' && $reinsurName === '') {
                continue;
            }

            $errors = [];

            if ($businessCode === '') {
                $errors[] = 'business_code is required.';
            } elseif (strlen($businessCode) > 19) {
                $errors[] = "business_code must be at most 19 characters (got " . strlen($businessCode) . ").";
            }

            if ($description === '') {
                $errors[] = 'description is required.';
            }

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

            $parentIdResolved = null;
            if ($treatyCode !== '') {
                if (! isset($treatyCodes[$treatyCode])) {
                    $errors[] = "Treaty Code not found: '{$treatyCode}'. Check REF_Treaties sheet.";
                } else {
                    $parentIdResolved = $treatyCode;
                }
            }

            $renewedFromResolved = null;
            if ($renewedFrom !== '') {
                if (! isset($existingBusinessCodes[$renewedFrom])) {
                    $errors[] = "Renewed From business_code does not exist: '{$renewedFrom}'.";
                } else {
                    $renewedFromResolved = $renewedFrom;
                }
            }

            $index = ($indexRaw !== null && $indexRaw !== '') ? (int) $indexRaw : 1;
            if ($index < 1) {
                $errors[] = "index must be a positive integer (got: {$index}).";
            }

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

        if (! empty($this->errorRows)) {
            $this->state = 'errors';
        } elseif (! empty($this->previewRows)) {
            $this->state = 'preview';
        } else {
            $this->state     = 'errors';
            $this->errorRows = [['row' => '—', 'business_code' => '—', 'errors' => ['No data rows found in the file. Make sure you filled the Businesses sheet.']]];
        }
    }

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
                    $existing->fill($payload)->save();
                    $updated++;
                } else {
                    Business::create(array_merge($payload, [
                        'business_code'   => $row['business_code'],
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

    public function resetState(): void
    {
        $this->state         = 'idle';
        $this->importFile    = null;
        $this->previewRows   = [];
        $this->errorRows     = [];
        $this->importedCount = 0;
        $this->insertedCount = 0;
        $this->updatedCount  = 0;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // STEP 2 — Cost Schemes
    // ══════════════════════════════════════════════════════════════════════════

    public function downloadCostSchemeTemplate(): StreamedResponse|BinaryFileResponse
    {
        return Excel::download(
            CostSchemeTemplateExport::build(),
            'step2_cost_schemes_import_template.xlsx'
        );
    }

    public function updatedCsImportFile(): void
    {
        $this->processCostSchemeFile();
    }

    public function processCostSchemeFile(): void
    {
        if (! $this->csImportFile) {
            return;
        }

        // ── Lookup maps ───────────────────────────────────────────────────────
        $deductionIds = Deduction::pluck('id')
            ->flip()
            ->toArray(); // id => true

        $partners = Partner::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [mb_strtolower(trim($name)) => $id])
            ->toArray();

        $existingSchemeIds = CostScheme::withTrashed()
            ->pluck('id')
            ->flip()
            ->toArray(); // id => true

        // ── Read Excel ────────────────────────────────────────────────────────
        $path = $this->csImportFile->getRealPath();
        $data = Excel::toArray(null, $path, null, \Maatwebsite\Excel\Excel::XLSX);

        if (empty($data[0]) && empty($data[1])) {
            $this->csState     = 'errors';
            $this->csErrorRows = [['sheet' => '—', 'row' => '—', 'key' => '—', 'errors' => ['The uploaded file appears to be empty or could not be read.']]];
            return;
        }

        $this->csPreviewSchemes = [];
        $this->csPreviewNodes   = [];
        $this->csErrorRows      = [];

        // ── Validate Sheet 0 — CostSchemes ───────────────────────────────────
        $seenNodeKeys = [];
        $schemeRows     = isset($data[0]) ? array_slice($data[0], 1) : []; // skip header
        $schemeIdsInFile = []; // scheme_id => true — for cross-validation

        foreach ($schemeRows as $i => $row) {
            $lineNo = $i + 2;
            $row    = array_pad((array) $row, 5, null);

            $schemeId      = trim((string) ($row[0] ?? ''));
            $indexRaw      = $row[1];
            $shareRaw      = $row[2];
            $agreementType = trim((string) ($row[3] ?? ''));
            $description   = trim((string) ($row[4] ?? ''));

            if ($schemeId === '' && $indexRaw === null && $shareRaw === null && $agreementType === '') {
                continue;
            }

            $errors = [];

            if ($schemeId === '') {
                $errors[] = 'scheme_id is required.';
            } elseif (strlen($schemeId) > 19) {
                $errors[] = "scheme_id must be at most 19 characters (got " . strlen($schemeId) . ").";
            }

            $index = ($indexRaw !== null && $indexRaw !== '') ? (int) $indexRaw : null;
            if ($index === null) {
                $errors[] = 'index is required.';
            } elseif ($index < 1) {
                $errors[] = "index must be a positive integer (got: {$index}).";
            }

            $share = ($shareRaw !== null && $shareRaw !== '') ? (float) $shareRaw : null;
            if ($share === null) {
                $errors[] = 'share is required.';
            }

            if ($agreementType === '') {
                $errors[] = 'agreement_type is required.';
            } elseif (! in_array($agreementType, self::AGREEMENT_TYPES, true)) {
                $errors[] = "Invalid agreement_type: '{$agreementType}'. Allowed: " . implode(', ', self::AGREEMENT_TYPES) . '.';
            }

            $isUpdate = isset($existingSchemeIds[$schemeId]);

            $rowData = [
                'sheet'          => 'CostSchemes',
                'row'            => $lineNo,
                'key'            => $schemeId,
                'scheme_id'      => $schemeId,
                'index'          => $index,
                'share'          => $share,
                'agreement_type' => $agreementType,
                'description'    => $description !== '' ? $description : null,
                '_is_update'     => $isUpdate,
            ];

            if (! empty($errors)) {
                $rowData['errors'] = $errors;
                $this->csErrorRows[] = $rowData;
            } else {
                $this->csPreviewSchemes[] = $rowData;
                $schemeIdsInFile[$schemeId] = true;
            }
        }

        // ── Validate Sheet 1 — CostNodesx ────────────────────────────────────
        $nodeRows = isset($data[1]) ? array_slice($data[1], 1) : [];

        // Combined valid scheme IDs: new ones from file + already existing in DB
        $allValidSchemeIds = $schemeIdsInFile + $existingSchemeIds;

        foreach ($nodeRows as $i => $row) {
            $lineNo = $i + 2;
            $row    = array_pad((array) $row, 7, null);

            $schemeId        = trim((string) ($row[0] ?? ''));
            $indexRaw        = $row[1];
            $deductionIdRaw  = $row[2];
            $valueRaw        = $row[3];
            $applyRaw        = strtolower(trim((string) ($row[4] ?? '')));
            $partnerSource   = trim((string) ($row[5] ?? ''));
            $partnerDest     = trim((string) ($row[6] ?? ''));

            if ($schemeId === '' && $indexRaw === null && $deductionIdRaw === null && $partnerSource === '') {
                continue;
            }

            $errors = [];

            if ($schemeId === '') {
                $errors[] = 'scheme_id is required.';
            } elseif (! isset($allValidSchemeIds[$schemeId])) {
                $errors[] = "scheme_id '{$schemeId}' not found in the CostSchemes sheet or in the system.";
            }

            $index = ($indexRaw !== null && $indexRaw !== '') ? (int) $indexRaw : null;
            if ($index === null) {
                $errors[] = 'index is required.';
            } elseif ($index < 1) {
                $errors[] = "index must be a positive integer (got: {$index}).";
            }

            $deductionId = ($deductionIdRaw !== null && $deductionIdRaw !== '') ? (int) $deductionIdRaw : null;
            if ($deductionId === null) {
                $errors[] = 'deduction_id is required.';
            } elseif (! isset($deductionIds[$deductionId])) {
                $errors[] = "Deduction ID {$deductionId} not found. Check REF_Deductions sheet.";
            }

            $value = ($valueRaw !== null && $valueRaw !== '') ? (float) $valueRaw : null;
            if ($value === null) {
                $errors[] = 'value is required.';
            }

            $applyToGross = in_array($applyRaw, ['yes', '1', 'true', 'si', 'sí'], true);

            $partnerSourceId = null;
            if ($partnerSource === '') {
                $errors[] = 'partner_source is required.';
            } else {
                $partnerSourceId = $partners[mb_strtolower($partnerSource)] ?? null;
                if ($partnerSourceId === null) {
                    $errors[] = "Partner source not found: '{$partnerSource}'. Check REF_Partners sheet.";
                }
            }

            $partnerDestId = null;
            if ($partnerDest === '') {
                $errors[] = 'partner_destination is required.';
            } else {
                $partnerDestId = $partners[mb_strtolower($partnerDest)] ?? null;
                if ($partnerDestId === null) {
                    $errors[] = "Partner destination not found: '{$partnerDest}'. Check REF_Partners sheet.";
                }
            }

            // Check for duplicate (scheme_id + index) within the file itself
            $nodeKey = "{$schemeId}#{$index}";
            if ($index !== null && $schemeId !== '') {
                if (isset($seenNodeKeys[$nodeKey])) {
                    $errors[] = "Duplicate node: scheme_id '{$schemeId}' + index {$index} appears more than once in the file.";
                } else {
                    $seenNodeKeys[$nodeKey] = true;
                }
            }

            // Determine if this node already exists in DB
            $existingNode = ($index !== null && $schemeId !== '' && empty($errors))
                ? CostNodex::where('cscheme_id', $schemeId)->where('index', $index)->first()
                : null;

            $rowData = [
                'sheet'                => 'CostNodesx',
                'row'                  => $lineNo,
                'key'                  => $nodeKey,
                'cscheme_id'           => $schemeId,
                'index'                => $index,
                'concept'              => $deductionId,
                'value'                => $value,
                'apply_to_gross'       => $applyToGross,
                'partner_source_id'    => $partnerSourceId,
                'partner_destination_id' => $partnerDestId,
                '_partner_source_name' => $partnerSource,
                '_partner_dest_name'   => $partnerDest,
                '_is_update'           => $existingNode !== null,
                '_existing_id'         => $existingNode?->id,
            ];

            if (! empty($errors)) {
                $rowData['errors'] = $errors;
                $this->csErrorRows[] = $rowData;
            } else {
                $this->csPreviewNodes[] = $rowData;
            }
        }


        // ── Decide state ──────────────────────────────────────────────────────
        if (! empty($this->csErrorRows)) {
            $this->csState = 'errors';
        } elseif (! empty($this->csPreviewSchemes) || ! empty($this->csPreviewNodes)) {
            $this->csState = 'preview';
        } else {
            $this->csState     = 'errors';
            $this->csErrorRows = [['sheet' => '—', 'row' => '—', 'key' => '—', 'errors' => ['No data rows found. Make sure you filled the CostSchemes and/or CostNodesx sheets.']]];
        }
    }

    public function confirmCostSchemeImport(): void
    {
        if ($this->csState !== 'preview') {
            return;
        }

        $insertedSchemes = 0;
        $updatedSchemes  = 0;
        $insertedNodes   = 0;
        $updatedNodes    = 0;

        DB::transaction(function () use (&$insertedSchemes, &$updatedSchemes, &$insertedNodes, &$updatedNodes) {
            // 1. Upsert schemes first
            foreach ($this->csPreviewSchemes as $row) {
                $payload = [
                    'index'          => $row['index'],
                    'share'          => $row['share'],
                    'agreement_type' => $row['agreement_type'],
                    'description'    => $row['description'],
                ];

                $existing = CostScheme::withTrashed()->find($row['scheme_id']);

                if ($existing) {
                    $existing->fill($payload)->save();
                    $updatedSchemes++;
                } else {
                    CostScheme::create(array_merge($payload, [
                        'id'              => $row['scheme_id'],
                        'created_by_user' => Auth::id(),
                    ]));
                    $insertedSchemes++;
                }
            }

            // 2. Upsert nodes (matched by cscheme_id + index)
            foreach ($this->csPreviewNodes as $row) {
                $payload = [
                    'concept'                => $row['concept'],
                    'value'                  => $row['value'],
                    'apply_to_gross'         => $row['apply_to_gross'],
                    'partner_source_id'      => $row['partner_source_id'],
                    'partner_destination_id' => $row['partner_destination_id'],
                    'cscheme_id'             => $row['cscheme_id'],
                    'index'                  => $row['index'],
                ];

                if ($row['_existing_id']) {
                    CostNodex::withTrashed()->find($row['_existing_id'])?->fill($payload)->save();
                    $updatedNodes++;
                } else {
                    CostNodex::create(array_merge($payload, [
                        'id' => (string) Str::uuid(),
                    ]));
                    $insertedNodes++;
                }
            }
        });

        $this->csImportedSchemes = $insertedSchemes + $updatedSchemes;
        $this->csInsertedSchemes = $insertedSchemes;
        $this->csUpdatedSchemes  = $updatedSchemes;
        $this->csImportedNodes   = $insertedNodes + $updatedNodes;
        $this->csInsertedNodes   = $insertedNodes;
        $this->csUpdatedNodes    = $updatedNodes;
        $this->csState           = 'imported';

        Notification::make()
            ->success()
            ->title('Cost Schemes import completed')
            ->body("{$insertedSchemes} schemes inserted · {$updatedSchemes} updated · {$insertedNodes} nodes inserted · {$updatedNodes} nodes updated")
            ->send();
    }

    public function resetCostSchemeState(): void
    {
        $this->csState          = 'idle';
        $this->csImportFile     = null;
        $this->csPreviewSchemes = [];
        $this->csPreviewNodes   = [];
        $this->csErrorRows      = [];
        $this->csImportedSchemes = 0;
        $this->csInsertedSchemes = 0;
        $this->csUpdatedSchemes  = 0;
        $this->csImportedNodes   = 0;
        $this->csInsertedNodes   = 0;
        $this->csUpdatedNodes    = 0;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // STEP 3 — Liability Structures
    // ══════════════════════════════════════════════════════════════════════════

    public function downloadLiabilityStructureTemplate(): StreamedResponse|BinaryFileResponse
    {
        return Excel::download(
            LiabilityStructureTemplateExport::build(),
            'step3_liability_structures_import_template.xlsx'
        );
    }

    public function updatedLsImportFile(): void
    {
        $this->processLiabilityStructureFile();
    }

    public function processLiabilityStructureFile(): void
    {
        if (! $this->lsImportFile) {
            return;
        }

        // ── Lookup maps ───────────────────────────────────────────────────────
        $coverages = Coverage::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [mb_strtolower(trim($name)) => $id])
            ->toArray();

        $existingBusinessCodes = Business::pluck('business_code')
            ->flip()
            ->toArray(); // business_code => true

        // ── Read Excel ────────────────────────────────────────────────────────
        $path = $this->lsImportFile->getRealPath();
        $data = Excel::toArray(null, $path, null, \Maatwebsite\Excel\Excel::XLSX);

        if (empty($data[0])) {
            $this->lsState     = 'errors';
            $this->lsErrorRows = [['row' => '—', 'business_code' => '—', 'errors' => ['The uploaded file appears to be empty or could not be read.']]];
            return;
        }

        $dataRows = array_slice($data[0], 1); // skip header row

        $this->lsPreviewRows = [];
        $this->lsErrorRows   = [];

        foreach ($dataRows as $i => $row) {
            $lineNo = $i + 2;
            $row    = array_pad((array) $row, 9, null);

            $businessCode   = trim((string) ($row[0] ?? ''));
            $coverageName   = trim((string) ($row[1] ?? ''));
            $clsRaw         = strtolower(trim((string) ($row[2] ?? '')));
            $limitRaw       = $row[3];
            $limitDesc      = trim((string) ($row[4] ?? ''));
            $sublimitRaw    = $row[5];
            $sublimitDesc   = trim((string) ($row[6] ?? ''));
            $deductibleRaw  = $row[7];
            $deductibleDesc = trim((string) ($row[8] ?? ''));

            // Skip empty rows
            if ($businessCode === '' && $coverageName === '' && $limitRaw === null) {
                continue;
            }

            $errors = [];

            // business_code
            if ($businessCode === '') {
                $errors[] = 'business_code is required.';
            } elseif (! isset($existingBusinessCodes[$businessCode])) {
                $errors[] = "Business not found: '{$businessCode}'. Check REF_Businesses sheet.";
            }

            // coverage_name → coverage_id
            $coverageId = null;
            if ($coverageName === '') {
                $errors[] = 'coverage_name is required.';
            } else {
                $coverageId = $coverages[mb_strtolower($coverageName)] ?? null;
                if ($coverageId === null) {
                    $errors[] = "Coverage not found: '{$coverageName}'. Check REF_Coverages sheet.";
                }
            }

            // cls (boolean)
            $cls = in_array($clsRaw, ['yes', '1', 'true', 'si', 'sí'], true);

            // limit (required float)
            $limit = ($limitRaw !== null && $limitRaw !== '') ? (float) $limitRaw : null;
            if ($limit === null) {
                $errors[] = 'limit is required.';
            }

            // limit_desc (required)
            if ($limitDesc === '') {
                $errors[] = 'limit_desc is required.';
            }

            // Optional fields
            $sublimit    = ($sublimitRaw !== null && $sublimitRaw !== '') ? (float) $sublimitRaw : null;
            $deductible  = ($deductibleRaw !== null && $deductibleRaw !== '') ? (float) $deductibleRaw : null;

            $rowData = [
                'row'             => $lineNo,
                'business_code'   => $businessCode,
                'coverage_id'     => $coverageId,
                'cls'             => $cls,
                'limit'           => $limit,
                'limit_desc'      => $limitDesc,
                'sublimit'        => $sublimit,
                'sublimit_desc'   => $sublimitDesc !== '' ? $sublimitDesc : null,
                'deductible'      => $deductible,
                'deductible_desc' => $deductibleDesc !== '' ? $deductibleDesc : null,
                '_coverage_name'  => $coverageName,
            ];

            if (! empty($errors)) {
                $rowData['errors'] = $errors;
                $this->lsErrorRows[] = $rowData;
            } else {
                $this->lsPreviewRows[] = $rowData;
            }
        }

        if (! empty($this->lsErrorRows)) {
            $this->lsState = 'errors';
        } elseif (! empty($this->lsPreviewRows)) {
            $this->lsState = 'preview';
        } else {
            $this->lsState     = 'errors';
            $this->lsErrorRows = [['row' => '—', 'business_code' => '—', 'errors' => ['No data rows found. Make sure you filled the LiabilityStructures sheet.']]];
        }
    }

    public function confirmLiabilityStructureImport(): void
    {
        if ($this->lsState !== 'preview' || empty($this->lsPreviewRows)) {
            return;
        }

        $inserted = 0;

        DB::transaction(function () use (&$inserted) {
            foreach ($this->lsPreviewRows as $row) {
                LiabilityStructure::create([
                    'business_code'   => $row['business_code'],
                    'coverage_id'     => $row['coverage_id'],
                    'cls'             => $row['cls'],
                    'limit'           => $row['limit'],
                    'limit_desc'      => $row['limit_desc'],
                    'sublimit'        => $row['sublimit'],
                    'sublimit_desc'   => $row['sublimit_desc'],
                    'deductible'      => $row['deductible'],
                    'deductible_desc' => $row['deductible_desc'],
                    // index is auto-assigned by the model's booted() creating hook
                ]);
                $inserted++;
            }
        });

        $this->lsInsertedCount = $inserted;
        $this->lsState         = 'imported';

        Notification::make()
            ->success()
            ->title('Liability Structures import completed')
            ->body("{$inserted} " . ($inserted === 1 ? 'record' : 'records') . ' inserted.')
            ->send();
    }

    public function resetLiabilityStructureState(): void
    {
        $this->lsState        = 'idle';
        $this->lsImportFile   = null;
        $this->lsPreviewRows  = [];
        $this->lsErrorRows    = [];
        $this->lsInsertedCount = 0;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // STEP 4 — Operative Documents
    // ══════════════════════════════════════════════════════════════════════════

    public function downloadOperativeDocTemplate(): StreamedResponse|BinaryFileResponse
    {
        return Excel::download(
            OperativeDocTemplateExport::build(),
            'step4_operative_docs_import_template.xlsx'
        );
    }

    public function updatedOdImportFile(): void
    {
        $this->processOperativeDocFile();
    }

    public function processOperativeDocFile(): void
    {
        if (! $this->odImportFile) {
            return;
        }

        // ── Lookup maps ───────────────────────────────────────────────────────
        $docTypes = BusinessDocType::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [mb_strtolower(trim($name)) => $id])
            ->toArray();

        $existingBusinessCodes = Business::pluck('business_code')
            ->flip()
            ->toArray();

        $existingDocIds = OperativeDoc::withTrashed()
            ->pluck('id')
            ->flip()
            ->toArray();

        // ── Read Excel ────────────────────────────────────────────────────────
        $path = $this->odImportFile->getRealPath();
        $data = Excel::toArray(null, $path, null, \Maatwebsite\Excel\Excel::XLSX);

        if (empty($data[0])) {
            $this->odState     = 'errors';
            $this->odErrorRows = [['row' => '—', 'id' => '—', 'errors' => ['The uploaded file appears to be empty or could not be read.']]];
            return;
        }

        $dataRows = array_slice($data[0], 1);

        $this->odPreviewRows = [];
        $this->odErrorRows   = [];

        foreach ($dataRows as $i => $row) {
            $lineNo = $i + 2;
            $row    = array_pad((array) $row, 9, null);

            $id             = trim((string) ($row[0] ?? ''));
            $businessCode   = trim((string) ($row[1] ?? ''));
            $docTypeName    = trim((string) ($row[2] ?? ''));
            $description    = trim((string) ($row[3] ?? ''));
            $inceptionRaw   = $row[4];
            $expirationRaw  = $row[5];
            $afMfRaw        = $row[6];
            $roeFsRaw       = $row[7];
            $repDateRaw     = $row[8];

            if ($id === '' && $businessCode === '' && $description === '' && $inceptionRaw === null) {
                continue;
            }

            $errors = [];

            if ($id === '') {
                $errors[] = 'id is required.';
            } elseif (strlen($id) > 19) {
                $errors[] = 'id must be at most 19 characters (got ' . strlen($id) . ').';
            }

            if ($businessCode === '') {
                $errors[] = 'business_code is required.';
            } elseif (! isset($existingBusinessCodes[$businessCode])) {
                $errors[] = "Business not found: '{$businessCode}'. Check REF_Businesses sheet.";
            }

            $docTypeId = null;
            if ($docTypeName === '') {
                $errors[] = 'doc_type_name is required.';
            } else {
                $docTypeId = $docTypes[mb_strtolower($docTypeName)] ?? null;
                if ($docTypeId === null) {
                    $errors[] = "Document type not found: '{$docTypeName}'. Check REF_DocTypes sheet.";
                }
            }

            if ($description === '') {
                $errors[] = 'description is required.';
            }

            $inceptionDate = $this->parseExcelDate($inceptionRaw);
            if ($inceptionDate === null) {
                $errors[] = 'inception_date is required and must be a valid date (YYYY-MM-DD).';
            }

            $expirationDate = $this->parseExcelDate($expirationRaw);
            if ($expirationDate === null) {
                $errors[] = 'expiration_date is required and must be a valid date (YYYY-MM-DD).';
            }

            $afMf = ($afMfRaw !== null && $afMfRaw !== '') ? (float) $afMfRaw : null;
            if ($afMf === null) {
                $errors[] = 'af_mf is required.';
            }

            $roeFs   = ($roeFsRaw !== null && $roeFsRaw !== '') ? (float) $roeFsRaw : null;
            $repDate = ($repDateRaw !== null && $repDateRaw !== '') ? $this->parseExcelDate($repDateRaw) : null;

            $isUpdate = isset($existingDocIds[$id]);

            $rowData = [
                'row'                   => $lineNo,
                'id'                    => $id,
                'business_code'         => $businessCode,
                'operative_doc_type_id' => $docTypeId,
                'description'           => $description,
                'inception_date'        => $inceptionDate,
                'expiration_date'       => $expirationDate,
                'af_mf'                 => $afMf,
                'roe_fs'                => $roeFs,
                'rep_date'              => $repDate,
                '_doc_type_name'        => $docTypeName,
                '_is_update'            => $isUpdate,
            ];

            if (! empty($errors)) {
                $rowData['errors'] = $errors;
                $this->odErrorRows[] = $rowData;
            } else {
                $this->odPreviewRows[] = $rowData;
            }
        }

        if (! empty($this->odErrorRows)) {
            $this->odState = 'errors';
        } elseif (! empty($this->odPreviewRows)) {
            $this->odState = 'preview';
        } else {
            $this->odState     = 'errors';
            $this->odErrorRows = [['row' => '—', 'id' => '—', 'errors' => ['No data rows found. Make sure you filled the OperativeDocs sheet.']]];
        }
    }

    public function confirmOperativeDocImport(): void
    {
        if ($this->odState !== 'preview' || empty($this->odPreviewRows)) {
            return;
        }

        $inserted = 0;
        $updated  = 0;

        DB::transaction(function () use (&$inserted, &$updated) {
            foreach ($this->odPreviewRows as $row) {
                $payload = [
                    'business_code'         => $row['business_code'],
                    'operative_doc_type_id' => $row['operative_doc_type_id'],
                    'description'           => $row['description'],
                    'inception_date'        => $row['inception_date'],
                    'expiration_date'       => $row['expiration_date'],
                    'af_mf'                 => $row['af_mf'],
                    'roe_fs'                => $row['roe_fs'],
                    'rep_date'              => $row['rep_date'],
                ];

                $existing = OperativeDoc::withTrashed()->find($row['id']);

                if ($existing) {
                    $existing->fill($payload)->save();
                    $updated++;
                } else {
                    OperativeDoc::create(array_merge($payload, [
                        'id'              => $row['id'],
                        'created_by_user' => Auth::id(),
                    ]));
                    $inserted++;
                }
            }
        });

        $this->odInsertedCount = $inserted;
        $this->odUpdatedCount  = $updated;
        $this->odState         = 'imported';

        Notification::make()
            ->success()
            ->title('Operative Documents import completed')
            ->body("{$inserted} inserted · {$updated} updated")
            ->send();
    }

    public function resetOperativeDocState(): void
    {
        $this->odState         = 'idle';
        $this->odImportFile    = null;
        $this->odPreviewRows   = [];
        $this->odErrorRows     = [];
        $this->odInsertedCount = 0;
        $this->odUpdatedCount  = 0;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // STEP 5 — Insureds
    // ══════════════════════════════════════════════════════════════════════════

    public function downloadInsuredTemplate(): StreamedResponse|BinaryFileResponse
    {
        return Excel::download(
            InsuredTemplateExport::build(),
            'step5_insureds_import_template.xlsx'
        );
    }

    public function updatedBiImportFile(): void
    {
        $this->processInsuredFile();
    }

    public function processInsuredFile(): void
    {
        if (! $this->biImportFile) {
            return;
        }

        // ── Lookup maps ───────────────────────────────────────────────────────
        $existingDocIds = OperativeDoc::pluck('id')
            ->flip()
            ->toArray(); // id => true

        $existingSchemeIds = CostScheme::pluck('id')
            ->flip()
            ->toArray(); // id => true

        $companies = Company::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [mb_strtolower(trim($name)) => $id])
            ->toArray();

        $coverages = Coverage::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [mb_strtolower(trim($name)) => $id])
            ->toArray();

        // ── Read Excel ────────────────────────────────────────────────────────
        $path = $this->biImportFile->getRealPath();
        $data = Excel::toArray(null, $path, null, \Maatwebsite\Excel\Excel::XLSX);

        if (empty($data[0])) {
            $this->biState     = 'errors';
            $this->biErrorRows = [['row' => '—', 'op_document_id' => '—', 'errors' => ['The uploaded file appears to be empty or could not be read.']]];
            return;
        }

        $dataRows = array_slice($data[0], 1);

        $this->biPreviewRows = [];
        $this->biErrorRows   = [];

        foreach ($dataRows as $i => $row) {
            $lineNo = $i + 2;
            $row    = array_pad((array) $row, 5, null);

            $opDocId      = trim((string) ($row[0] ?? ''));
            $cschemeId    = trim((string) ($row[1] ?? ''));
            $companyName  = trim((string) ($row[2] ?? ''));
            $coverageName = trim((string) ($row[3] ?? ''));
            $premiumRaw   = $row[4];

            if ($opDocId === '' && $cschemeId === '' && $companyName === '' && $coverageName === '') {
                continue;
            }

            $errors = [];

            if ($opDocId === '') {
                $errors[] = 'op_document_id is required.';
            } elseif (! isset($existingDocIds[$opDocId])) {
                $errors[] = "Operative document not found: '{$opDocId}'. Check REF_OperativeDocs sheet.";
            }

            if ($cschemeId === '') {
                $errors[] = 'cscheme_id is required.';
            } elseif (! isset($existingSchemeIds[$cschemeId])) {
                $errors[] = "Cost scheme not found: '{$cschemeId}'. Check REF_CostSchemes sheet.";
            }

            $companyId = null;
            if ($companyName === '') {
                $errors[] = 'company_name is required.';
            } else {
                $companyId = $companies[mb_strtolower($companyName)] ?? null;
                if ($companyId === null) {
                    $errors[] = "Company not found: '{$companyName}'. Check REF_Companies sheet.";
                }
            }

            $coverageId = null;
            if ($coverageName === '') {
                $errors[] = 'coverage_name is required.';
            } else {
                $coverageId = $coverages[mb_strtolower($coverageName)] ?? null;
                if ($coverageId === null) {
                    $errors[] = "Coverage not found: '{$coverageName}'. Check REF_Coverages sheet.";
                }
            }

            $premium = ($premiumRaw !== null && $premiumRaw !== '') ? (float) $premiumRaw : null;
            if ($premium === null) {
                $errors[] = 'premium is required.';
            }

            $rowData = [
                'row'            => $lineNo,
                'op_document_id' => $opDocId,
                'cscheme_id'     => $cschemeId,
                'company_id'     => $companyId,
                'coverage_id'    => $coverageId,
                'premium'        => $premium,
                '_company_name'  => $companyName,
                '_coverage_name' => $coverageName,
            ];

            if (! empty($errors)) {
                $rowData['errors'] = $errors;
                $this->biErrorRows[] = $rowData;
            } else {
                $this->biPreviewRows[] = $rowData;
            }
        }

        if (! empty($this->biErrorRows)) {
            $this->biState = 'errors';
        } elseif (! empty($this->biPreviewRows)) {
            $this->biState = 'preview';
        } else {
            $this->biState     = 'errors';
            $this->biErrorRows = [['row' => '—', 'op_document_id' => '—', 'errors' => ['No data rows found. Make sure you filled the Insureds sheet.']]];
        }
    }

    public function confirmInsuredImport(): void
    {
        if ($this->biState !== 'preview' || empty($this->biPreviewRows)) {
            return;
        }

        $inserted = 0;

        DB::transaction(function () use (&$inserted) {
            foreach ($this->biPreviewRows as $row) {
                BusinessOpDocsInsured::create([
                    'op_document_id' => $row['op_document_id'],
                    'cscheme_id'     => $row['cscheme_id'],
                    'company_id'     => $row['company_id'],
                    'coverage_id'    => $row['coverage_id'],
                    'premium'        => $row['premium'],
                    // id auto-generated as UUID by booted()::creating
                ]);
                $inserted++;
            }
        });

        $this->biInsertedCount = $inserted;
        $this->biState         = 'imported';

        Notification::make()
            ->success()
            ->title('Insureds import completed')
            ->body("{$inserted} " . ($inserted === 1 ? 'record' : 'records') . ' inserted.')
            ->send();
    }

    public function resetInsuredState(): void
    {
        $this->biState        = 'idle';
        $this->biImportFile   = null;
        $this->biPreviewRows  = [];
        $this->biErrorRows    = [];
        $this->biInsertedCount = 0;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // STEP 6 — Document Cost Schemes
    // ══════════════════════════════════════════════════════════════════════════

    public function downloadDocSchemeTemplate(): StreamedResponse|BinaryFileResponse
    {
        return Excel::download(
            DocSchemeTemplateExport::build(),
            'step6_doc_schemes_import_template.xlsx'
        );
    }

    public function updatedDsImportFile(): void
    {
        $this->processDocSchemeFile();
    }

    public function processDocSchemeFile(): void
    {
        if (! $this->dsImportFile) {
            return;
        }

        // ── Lookup maps ───────────────────────────────────────────────────────
        $existingDocIds = OperativeDoc::pluck('id')
            ->flip()
            ->toArray();

        $existingSchemeIds = CostScheme::pluck('id')
            ->flip()
            ->toArray();

        // ── Read Excel ────────────────────────────────────────────────────────
        $path = $this->dsImportFile->getRealPath();
        $data = Excel::toArray(null, $path, null, \Maatwebsite\Excel\Excel::XLSX);

        if (empty($data[0])) {
            $this->dsState     = 'errors';
            $this->dsErrorRows = [['row' => '—', 'op_document_id' => '—', 'errors' => ['The uploaded file appears to be empty or could not be read.']]];
            return;
        }

        $dataRows = array_slice($data[0], 1);

        $this->dsPreviewRows = [];
        $this->dsErrorRows   = [];

        foreach ($dataRows as $i => $row) {
            $lineNo    = $i + 2;
            $row       = array_pad((array) $row, 2, null);

            $opDocId   = trim((string) ($row[0] ?? ''));
            $cschemeId = trim((string) ($row[1] ?? ''));

            if ($opDocId === '' && $cschemeId === '') {
                continue;
            }

            $errors = [];

            if ($opDocId === '') {
                $errors[] = 'op_document_id is required.';
            } elseif (! isset($existingDocIds[$opDocId])) {
                $errors[] = "Operative document not found: '{$opDocId}'. Check REF_OperativeDocs sheet.";
            }

            if ($cschemeId === '') {
                $errors[] = 'cscheme_id is required.';
            } elseif (! isset($existingSchemeIds[$cschemeId])) {
                $errors[] = "Cost scheme not found: '{$cschemeId}'. Check REF_CostSchemes sheet.";
            }

            $rowData = [
                'row'            => $lineNo,
                'op_document_id' => $opDocId,
                'cscheme_id'     => $cschemeId,
            ];

            if (! empty($errors)) {
                $rowData['errors'] = $errors;
                $this->dsErrorRows[] = $rowData;
            } else {
                $this->dsPreviewRows[] = $rowData;
            }
        }

        if (! empty($this->dsErrorRows)) {
            $this->dsState = 'errors';
        } elseif (! empty($this->dsPreviewRows)) {
            $this->dsState = 'preview';
        } else {
            $this->dsState     = 'errors';
            $this->dsErrorRows = [['row' => '—', 'op_document_id' => '—', 'errors' => ['No data rows found. Make sure you filled the DocSchemes sheet.']]];
        }
    }

    public function confirmDocSchemeImport(): void
    {
        if ($this->dsState !== 'preview' || empty($this->dsPreviewRows)) {
            return;
        }

        $inserted = 0;

        DB::transaction(function () use (&$inserted) {
            foreach ($this->dsPreviewRows as $row) {
                BusinessOpDocsScheme::create([
                    'op_document_id' => $row['op_document_id'],
                    'cscheme_id'     => $row['cscheme_id'],
                    // id (UUID) and index auto-assigned by booted()::creating
                ]);
                $inserted++;
            }
        });

        $this->dsInsertedCount = $inserted;
        $this->dsState         = 'imported';

        Notification::make()
            ->success()
            ->title('Document Cost Schemes import completed')
            ->body("{$inserted} " . ($inserted === 1 ? 'record' : 'records') . ' inserted.')
            ->send();
    }

    public function resetDocSchemeState(): void
    {
        $this->dsState         = 'idle';
        $this->dsImportFile    = null;
        $this->dsPreviewRows   = [];
        $this->dsErrorRows     = [];
        $this->dsInsertedCount = 0;
    }

    private function parseExcelDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof \DateTime) {
            return Carbon::instance($value)->format('Y-m-d');
        }
        if (is_numeric($value)) {
            try {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                )->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }
        if (is_string($value)) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }
        return null;
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
