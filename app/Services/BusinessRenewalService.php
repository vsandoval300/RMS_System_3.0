<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Models\Business;
use App\Models\BusinessOpDocsInsured;
use App\Models\BusinessOpDocsScheme;
use App\Models\CostNodex;
use App\Models\CostScheme;
use App\Models\LiabilityStructure;
use App\Models\OperativeDoc;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessRenewalService
{
    /**
     * Renew a Business: clone Business + LiabilityStructures + Slip OperativeDoc
     * (with advanced dates) + independent CostSchemes/CostNodex + businessdoc_schemes
     * + businessdoc_insureds. All inside a single DB transaction.
     */
    public function renew(
        Business $original,
        string   $newBusinessCode,
        ?string  $inceptionDate   = null,
        ?string  $expirationDate  = null,
    ): Business {
        return DB::transaction(function () use ($original, $newBusinessCode, $inceptionDate, $expirationDate) {
            $newBusiness = $this->cloneBusiness($original, $newBusinessCode);

            $this->cloneLiabilityStructures($original, $newBusiness);

            $slip = $original->operativeDocs()
                ->where('operative_doc_type_id', 1)
                ->orderByDesc('index')
                ->first();

            if ($slip) {
                $newSlip   = $this->cloneSlip($slip, $newBusiness, $inceptionDate, $expirationDate);
                $schemeMap = $this->cloneDocSchemes($slip, $newSlip);
                $this->cloneDocInsureds($slip, $newSlip, $schemeMap);
            }

            return $newBusiness;
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  1. Business
    // ─────────────────────────────────────────────────────────────────────────

    private function cloneBusiness(Business $original, string $newBusinessCode): Business
    {
        $nextIndex = (Business::max('index') ?? 0) + 1;

        return Business::create([
            'business_code'    => $newBusinessCode,
            'index'            => $nextIndex,
            'description'      => $original->description,
            'reinsurance_type' => $original->reinsurance_type,
            'risk_covered'     => $original->risk_covered,
            'business_type'    => $original->business_type,
            'premium_type'     => $original->premium_type,
            'purpose'          => $original->purpose,
            'claims_type'      => $original->claims_type,
            'reinsurer_id'     => $original->reinsurer_id,
            'parent_id'        => null, // renewals are standalone, not inherited from treaty
            'producer_id'      => $original->producer_id,
            'currency_id'      => $original->currency_id,
            'region_id'        => $original->region_id,
            'renewed_from_id'  => $original->business_code,
            'approval_status'  => ApprovalStatus::PENDING,
            'created_by_user'  => Auth::id(),
            // business_lifecycle_status → ON_HOLD set automatically by Business::booted()
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  2. Liability Structures
    // ─────────────────────────────────────────────────────────────────────────

    private function cloneLiabilityStructures(Business $original, Business $newBusiness): void
    {
        $original->liabilityStructures()->get()
            ->each(function (LiabilityStructure $ls) use ($newBusiness) {
                LiabilityStructure::create([
                    'business_code'   => $newBusiness->business_code,
                    'coverage_id'     => $ls->coverage_id,
                    'country_id'      => $ls->country_id,
                    'cls'             => $ls->cls,
                    'limit'           => $ls->limit,
                    'limit_desc'      => $ls->limit_desc,
                    'sublimit'        => $ls->sublimit,
                    'sublimit_desc'   => $ls->sublimit_desc,
                    'deductible'      => $ls->deductible,
                    'deductible_desc' => $ls->deductible_desc,
                    // index auto-assigned by LiabilityStructure::booted()
                ]);
            });
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  3. Slip OperativeDoc — advance dates by one period
    // ─────────────────────────────────────────────────────────────────────────

    private function cloneSlip(
        OperativeDoc $slip,
        Business     $newBusiness,
        ?string      $inceptionDate  = null,
        ?string      $expirationDate = null,
    ): OperativeDoc {
        $inception  = Carbon::parse($slip->inception_date)->startOfDay();
        $expiration = Carbon::parse($slip->expiration_date)->startOfDay();
        $days       = $inception->diffInDays($expiration);

        // Use user-provided dates if available, otherwise advance by one period
        $newInception  = $inceptionDate  ? Carbon::parse($inceptionDate)->startOfDay()  : $expiration->copy();
        $newExpiration = $expirationDate ? Carbon::parse($expirationDate)->startOfDay() : $expiration->copy()->addDays($days);

        // ID format: {business_code}-{index:02d}
        // First doc of the new business → index will be 1 (confirmed by booted() hook)
        $newId = $newBusiness->business_code . '-' . str_pad(1, 2, '0', STR_PAD_LEFT);

        return OperativeDoc::create([
            'id'                    => $newId,
            'business_code'         => $newBusiness->business_code,
            'operative_doc_type_id' => 1, // Slip
            'description'           => $slip->description,
            'inception_date'        => $newInception,
            'expiration_date'       => $newExpiration,
            'af_mf'                 => $slip->af_mf,
            'roe_fs'                => $slip->roe_fs,
            'rep_date'              => $slip->rep_date,
            'created_by_user'       => Auth::id(),
            // index auto-assigned by OperativeDoc::booted() creating hook → 1
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  4. Cost Schemes + Cost Nodesx  (cloned independently)
    //     Returns a map: old_scheme_id => new_scheme_id
    // ─────────────────────────────────────────────────────────────────────────

    private function cloneDocSchemes(OperativeDoc $originalSlip, OperativeDoc $newSlip): array
    {
        $schemeMap = [];

        $originalSlip->schemes()->with('costScheme.costNodexes')->get()
            ->each(function (BusinessOpDocsScheme $docScheme) use ($newSlip, &$schemeMap) {
                $original = $docScheme->costScheme;

                if (! $original) {
                    return;
                }

                // ── 4a. Clone CostScheme with a new structured ID ──────────
                $nextIndex  = (CostScheme::withTrashed()->max('index') ?? 0) + 1;
                $newSchemeId = sprintf('SCHE-%s-%04d', now()->format('Ymd'), $nextIndex);

                $newScheme = CostScheme::create([
                    'id'              => $newSchemeId,
                    'index'           => $nextIndex,
                    'agreement_type'  => $original->agreement_type,
                    'share'           => $original->share,
                    'description'     => $original->description,
                    'created_by_user' => Auth::id(),
                ]);

                // ── 4b. Clone each CostNodex ───────────────────────────────
                $original->costNodexes()->orderBy('index')->get()
                    ->each(function (CostNodex $node) use ($newScheme, $newSchemeId) {
                        CostNodex::create([
                            'id'                     => $newSchemeId . '-' . Str::lower(Str::ulid()->toBase32()),
                            'index'                  => $node->index,
                            'concept'                => $node->concept,
                            'partner_source_id'      => $node->partner_source_id,
                            'partner_destination_id' => $node->partner_destination_id,
                            'value'                  => $node->value,
                            'apply_to_gross'         => $node->apply_to_gross,
                            'cscheme_id'             => $newScheme->id,
                        ]);
                    });

                // ── 4c. Link new scheme to new OperativeDoc ────────────────
                BusinessOpDocsScheme::create([
                    'op_document_id' => $newSlip->id,
                    'cscheme_id'     => $newScheme->id,
                    // index auto-assigned by BusinessOpDocsScheme::booted()
                ]);

                $schemeMap[$original->id] = $newScheme->id;
            });

        return $schemeMap;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  5. Businessdoc Insureds
    //     schemeMap ensures insureds point to the new cloned scheme, not the original
    // ─────────────────────────────────────────────────────────────────────────

    private function cloneDocInsureds(OperativeDoc $originalSlip, OperativeDoc $newSlip, array $schemeMap): void
    {
        $originalSlip->insureds()->get()
            ->each(function (BusinessOpDocsInsured $insured) use ($newSlip, $schemeMap) {
                BusinessOpDocsInsured::create([
                    'op_document_id' => $newSlip->id,
                    'cscheme_id'     => $schemeMap[$insured->cscheme_id] ?? $insured->cscheme_id,
                    'company_id'     => $insured->company_id,
                    'coverage_id'    => $insured->coverage_id,
                    'premium'        => $insured->premium,
                    // id auto-assigned by BusinessOpDocsInsured::booted()
                ]);
            });
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Suggest the next business code for the renewal.
     * Format: {year of new inception}-{middle part of original code}-{next 3-digit index}
     *
     * Example: original "2024-MAY010-003", new inception 2025 → "2025-MAY010-001" (if none exist yet)
     */
    public function suggestBusinessCode(Business $original): string
    {
        $code      = $original->business_code;
        $firstDash = strpos($code, '-');
        $lastDash  = strrpos($code, '-');

        if ($firstDash === false || $firstDash === $lastDash) {
            return $code . '-REN';
        }

        $middle = substr($code, $firstDash + 1, $lastDash - $firstDash - 1);

        // New year comes from the expiration date of the original Slip
        $slip = $original->operativeDocs()
            ->where('operative_doc_type_id', 1)
            ->orderByDesc('index')
            ->first();

        $newYear = $slip
            ? Carbon::parse($slip->expiration_date)->year
            : now()->year;

        $prefix = "{$newYear}-{$middle}-";

        $maxCode = Business::withTrashed()
            ->where('business_code', 'like', $prefix . '%')
            ->orderByDesc('business_code')
            ->value('business_code');

        $nextIndex = $maxCode
            ? (int) substr($maxCode, strrpos($maxCode, '-') + 1) + 1
            : 1;

        return $prefix . str_pad($nextIndex, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Return the new slip inception/expiration dates for display in the modal.
     * Returns ['inception' => Carbon, 'expiration' => Carbon] or null if no Slip found.
     */
    public function previewSlipDates(Business $original): ?array
    {
        $slip = $original->operativeDocs()
            ->where('operative_doc_type_id', 1)
            ->orderByDesc('index')
            ->first();

        if (! $slip) {
            return null;
        }

        $inception  = Carbon::parse($slip->inception_date)->startOfDay();
        $expiration = Carbon::parse($slip->expiration_date)->startOfDay();
        $days       = $inception->diffInDays($expiration);

        return [
            'inception'  => $expiration->copy(),
            'expiration' => $expiration->copy()->addDays($days),
        ];
    }
}
