<x-filament-panels::page>
<style>
    .biz-import {
        --bc-card:         light-dark(#ffffff, #18181B);
        --bc-border:       light-dark(#e5e7eb, #27272a);
        --bc-input-bg:     light-dark(#ffffff, #252527);
        --bc-text:         light-dark(#111827, #f4f4f5);
        --bc-text-sec:     light-dark(#374151, #d4d4d8);
        --bc-text-muted:   light-dark(#6b7280, #a1a1aa);
        --bc-row-hover:    light-dark(#f9fafb, #1c1c1f);
        --bc-tbl-head:     light-dark(#f3f4f6, #1D1D20);
        --bc-success:      #16a34a;
        --bc-danger:       #dc2626;
        --bc-warning:      #d97706;
    }

    /* ── Layout ── */
    .biz-import-wrap   { display: flex; flex-direction: column; gap: 1.25rem; }
    .biz-import-card   { background: var(--bc-card); border: 1px solid var(--bc-border); border-radius: 0.75rem; overflow: hidden; }
    .biz-import-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--bc-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }
    .biz-import-body   { padding: 1.25rem; }
    .biz-import-footer { padding: 0.9rem 1.25rem; border-top: 1px solid var(--bc-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }

    /* ── Buttons ── */
    .btn-primary  { display:inline-flex; align-items:center; gap:0.4rem; background:#41A2C3; color:#fff; border:none; border-radius:0.45rem; font-size:0.875rem; font-weight:600; padding:0.45rem 0.85rem; cursor:pointer; text-decoration:none; transition:background .12s; }
    .btn-primary:hover  { background:#3290af; }
    .btn-cancel   { display:inline-flex; align-items:center; gap:0.4rem; background:light-dark(#f3f4f6,#09090B); color:var(--bc-text-sec); border:1px solid var(--bc-border); border-radius:0.45rem; font-size:0.875rem; font-weight:600; padding:0.45rem 0.85rem; cursor:pointer; text-decoration:none; transition:background .12s; }
    .btn-cancel:hover   { background:var(--bc-border); }
    .btn-danger   { display:inline-flex; align-items:center; gap:0.4rem; background:#dc2626; color:#fff; border:none; border-radius:0.45rem; font-size:0.875rem; font-weight:600; padding:0.45rem 0.85rem; cursor:pointer; transition:background .12s; }
    .btn-danger:hover   { background:#b91c1c; }

    /* ── File upload ── */
    .biz-upload-zone { border: 2px dashed var(--bc-border); border-radius: 0.6rem; padding: 2rem; text-align: center; background: var(--bc-input-bg); transition: border-color .15s; }
    .biz-upload-zone:hover { border-color: #41A2C3; }
    .biz-upload-zone input[type=file] { display: none; }
    .biz-upload-zone label { display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.875rem; font-weight: 500; color: #41A2C3; }
    .biz-upload-hint { margin-top: 0.5rem; font-size: 0.82rem; color: var(--bc-text-muted); }

    /* ── Step badges ── */
    .biz-step-badge { display:inline-flex; align-items:center; justify-content:center; width:1.5rem; height:1.5rem; border-radius:50%; font-size:0.75rem; font-weight:700; flex-shrink:0; }
    .biz-step-badge.active  { background:#41A2C3; color:#fff; }
    .biz-step-badge.pending { background:var(--bc-tbl-head); color:var(--bc-text-muted); }

    /* ── Section titles ── */
    .biz-section-title { display:flex; align-items:center; gap:0.6rem; font-size:0.875rem; font-weight:600; color:var(--bc-text); margin-bottom:0.75rem; }

    /* ── Alert banners ── */
    .biz-alert { border-radius:0.6rem; padding:0.75rem 1rem; font-size:0.875rem; display:flex; align-items:flex-start; gap:0.6rem; }
    .biz-alert.error   { background:light-dark(#fef2f2,#2d1010); border:1px solid light-dark(#fecaca,#7f1d1d); color:light-dark(#b91c1c,#fca5a5); }
    .biz-alert.success { background:light-dark(#f0fdf4,#0d2110); border:1px solid light-dark(#bbf7d0,#14532d); color:light-dark(#15803d,#86efac); }
    .biz-alert.info    { background:light-dark(#eff6ff,#0d1926); border:1px solid light-dark(#bfdbfe,#1e3a5f); color:light-dark(#1d4ed8,#93c5fd); }

    /* ── Tables ── */
    .biz-tbl-wrap { overflow-x: auto; }
    .biz-tbl { width:100%; border-collapse:collapse; font-size:0.82rem; }
    .biz-tbl thead tr { background: var(--bc-tbl-head); }
    .biz-tbl th { padding:0.5rem 0.6rem; font-size:0.78rem; font-weight:600; color:var(--bc-text-sec); border-bottom:1px solid var(--bc-border); white-space:nowrap; text-align:left; }
    .biz-tbl tbody tr { border-bottom:1px solid var(--bc-border); transition:background .1s; }
    .biz-tbl tbody tr:hover { background: var(--bc-row-hover); }
    .biz-tbl td { padding:0.45rem 0.6rem; color:var(--bc-text-sec); vertical-align:top; }
    .biz-tbl td.mono { font-family:monospace; font-size:0.8rem; }
    .biz-tbl td.center { text-align:center; }
    .biz-tbl td.muted { color: var(--bc-text-muted); }

    /* ── Pill badges ── */
    .pill { display:inline-flex; align-items:center; padding:0.18rem 0.55rem; border-radius:999px; font-size:0.72rem; font-weight:600; white-space:nowrap; }
    .pill.new    { background:light-dark(#dbeafe,#1e3a5f); color:light-dark(#1d4ed8,#93c5fd); }
    .pill.update { background:light-dark(#fef3c7,#2d1f00); color:light-dark(#92400e,#fcd34d); }
    .pill.error  { background:light-dark(#fee2e2,#2d0f0f); color:light-dark(#b91c1c,#fca5a5); }

    /* ── Error list inside cell ── */
    .err-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0.25rem; }
    .err-list li::before { content:'• '; color:var(--bc-danger); }
    .err-list li { color:light-dark(#b91c1c,#fca5a5); font-size:0.78rem; }

    /* ── Stats row ── */
    .biz-stats { display:flex; gap:1.5rem; flex-wrap:wrap; }
    .biz-stat  { display:flex; flex-direction:column; gap:0.1rem; }
    .biz-stat-val { font-size:1.5rem; font-weight:700; color:var(--bc-text); font-variant-numeric:tabular-nums; }
    .biz-stat-lbl { font-size:0.78rem; color:var(--bc-text-muted); }
</style>

<div class="biz-import">
<div class="biz-import-wrap">

    {{-- ══════════════════════════════════════════════════════════
         STATE: idle — template download + file upload
    ══════════════════════════════════════════════════════════ --}}
    @if($state === 'idle')

    <div class="biz-import-card">
        <div class="biz-import-header">
            <div>
                <div style="display:inline-flex; align-items:center; gap:0.4rem; font-size:1.15rem; font-weight:700; color:#41A2C3; margin-bottom:0.3rem;">
                    <svg style="width:20px;height:20px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Step 1 &nbsp;·&nbsp; Businesses Table
                </div>
                <div style="font-size:1rem; font-weight:700; color:var(--bc-text);">Import Businesses from Excel</div>
                <div style="font-size:0.82rem; color:var(--bc-text-muted); margin-top:0.15rem;">
                    Download the template, fill it in, then upload it. All rows are validated before importing.
                </div>
            </div>
        </div>
        <div class="biz-import-body" style="display:flex; flex-direction:column; gap:1.5rem;">

            {{-- Step 1: Download Template --}}
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">1</span>
                    Download the Excel Template
                </div>
                <div class="biz-alert info" style="margin-bottom:0.85rem;">
                    <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <span>The template includes a <strong>Businesses</strong> data sheet and six reference sheets (Reinsurers, Partners, Currencies, Regions, Treaties, README). Look up exact names in the reference sheets before filling the data sheet.</span>
                </div>
                <button wire:click="downloadTemplate" class="btn-primary">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                    Download Template
                </button>
            </div>

            <div style="border-top:1px solid var(--bc-border);"></div>

            {{-- Step 2: Upload filled file --}}
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">2</span>
                    Upload Completed File (.xlsx)
                </div>
                <div class="biz-upload-zone" x-data="{ dragging: false }"
                     @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                     @drop.prevent="dragging=false; $refs.fileInput.files=$event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                     :style="dragging ? 'border-color:#41A2C3;' : ''">
                    <input type="file" x-ref="fileInput" wire:model="importFile" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="biz-file-input">
                    <label for="biz-file-input">
                        <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                        Click to select or drag & drop a .xlsx file
                    </label>
                    <div class="biz-upload-hint">Only .xlsx files. Must use the template format.</div>
                    <div wire:loading wire:target="importFile" style="margin-top:0.6rem; font-size:0.82rem; color:#41A2C3;">
                        Validating file…
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATE: errors — validation failed, abort
    ══════════════════════════════════════════════════════════ --}}
    @elseif($state === 'errors')

    <div class="biz-import-card">
        <div class="biz-import-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <svg style="width:20px;height:20px;color:#dc2626;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                <div>
                    <div style="font-size:1rem; font-weight:700; color:light-dark(#b91c1c,#fca5a5);">
                        Validation failed — {{ count($errorRows) }} {{ count($errorRows) === 1 ? 'error' : 'errors' }} found
                    </div>
                    <div style="font-size:0.82rem; color:var(--bc-text-muted); margin-top:0.1rem;">
                        Fix the issues below in your Excel file and re-upload. The entire import was aborted.
                    </div>
                </div>
            </div>
            <button wire:click="resetState" class="btn-cancel">← Upload New File</button>
        </div>

        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead>
                        <tr>
                            <th style="width:5rem;">Row</th>
                            <th style="width:14rem;">Business Code</th>
                            <th>Errors</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($errorRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['business_code'] ?: '—' }}</td>
                            <td>
                                <ul class="err-list">
                                    @foreach($row['errors'] as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATE: preview — all valid, confirm before import
    ══════════════════════════════════════════════════════════ --}}
    @elseif($state === 'preview')

    @php
        $newCount    = collect($previewRows)->where('_is_update', false)->count();
        $updateCount = collect($previewRows)->where('_is_update', true)->count();
    @endphp

    <div class="biz-import-card">
        <div class="biz-import-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <svg style="width:20px;height:20px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <div>
                    <div style="font-size:1rem; font-weight:700; color:var(--bc-text);">
                        Preview — {{ count($previewRows) }} {{ count($previewRows) === 1 ? 'record' : 'records' }} ready to import
                    </div>
                    <div style="font-size:0.82rem; color:var(--bc-text-muted); margin-top:0.1rem;">
                        All rows passed validation. Review below and confirm to proceed.
                    </div>
                </div>
            </div>
            <div style="display:flex; gap:0.5rem; align-items:center;">
                <span style="font-size:0.82rem; color:var(--bc-text-muted);">
                    <span class="pill new">{{ $newCount }} new</span>
                    &nbsp;
                    <span class="pill update">{{ $updateCount }} update</span>
                </span>
            </div>
        </div>

        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead>
                        <tr>
                            <th>Row</th>
                            <th>Business Code</th>
                            <th>Description</th>
                            <th>Reinsu. Type</th>
                            <th>Risk</th>
                            <th>Biz. Type</th>
                            <th>Premium</th>
                            <th>Reinsurer</th>
                            <th>Currency</th>
                            <th>Region</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['business_code'] }}</td>
                            <td style="max-width:18rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['description'] }}">
                                {{ $row['description'] }}
                            </td>
                            <td>{{ $row['reinsurance_type'] }}</td>
                            <td>{{ $row['risk_covered'] }}</td>
                            <td>{{ $row['business_type'] }}</td>
                            <td>{{ $row['premium_type'] }}</td>
                            <td style="max-width:12rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['_reinsurer_name'] }}">
                                {{ $row['_reinsurer_name'] }}
                            </td>
                            <td>{{ $row['_currency_code'] }}</td>
                            <td>{{ $row['_region_name'] }}</td>
                            <td>
                                @if($row['_is_update'])
                                    <span class="pill update">Update</span>
                                @else
                                    <span class="pill new">New</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="biz-import-footer">
            <button wire:click="resetState" class="btn-cancel">
                ← Cancel
            </button>
            <button wire:click="confirmImport"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                    class="btn-primary">
                <span wire:loading.remove wire:target="confirmImport">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Confirm Import — {{ count($previewRows) }} records
                </span>
                <span wire:loading wire:target="confirmImport">Importing…</span>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATE: imported — success summary
    ══════════════════════════════════════════════════════════ --}}
    @elseif($state === 'imported')

    <div class="biz-import-card">
        <div class="biz-import-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <svg style="width:22px;height:22px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <div style="font-size:1rem; font-weight:700; color:var(--bc-text);">Import Completed</div>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-alert success" style="margin-bottom:1.5rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span>{{ $importedCount }} {{ $importedCount === 1 ? 'business' : 'businesses' }} processed successfully.</span>
            </div>
            <div class="biz-stats">
                <div class="biz-stat">
                    <div class="biz-stat-val" style="color:#41A2C3;">{{ $importedCount }}</div>
                    <div class="biz-stat-lbl">Total processed</div>
                </div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat">
                    <div class="biz-stat-val" style="color:#16a34a;">{{ $insertedCount }}</div>
                    <div class="biz-stat-lbl">New records inserted</div>
                </div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat">
                    <div class="biz-stat-val" style="color:#d97706;">{{ $updatedCount }}</div>
                    <div class="biz-stat-lbl">Existing records updated</div>
                </div>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetState" class="btn-cancel">Import Another File</button>
            <a href="{{ \App\Filament\Resources\Businesses\BusinessResource::getUrl('index') }}" class="btn-primary">
                View Businesses →
            </a>
        </div>
    </div>

    @endif

</div>
</div>

</x-filament-panels::page>
