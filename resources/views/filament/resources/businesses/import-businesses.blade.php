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
    .biz-import-wrap { display: flex; flex-direction: column; gap: 1.25rem; }
    .biz-import-card { background: var(--bc-card); border: 1px solid var(--bc-border); border-radius: 0.75rem; overflow: hidden; }
    .biz-import-body   { padding: 1.25rem; }
    .biz-import-footer { padding: 0.9rem 1.25rem; border-top: 1px solid var(--bc-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }

    /* ── Accordion trigger ── */
    .biz-accordion-btn {
        display: flex; align-items: center; gap: 0.75rem;
        width: 100%; padding: 1rem 1.25rem;
        background: none; border: none; cursor: pointer; text-align: left;
        transition: background 0.12s;
    }
    .biz-accordion-btn:hover { background: var(--bc-row-hover); }
    .biz-accordion-btn--open { border-bottom: 1px solid var(--bc-border); }
    .biz-accordion-chevron {
        flex-shrink: 0; flex-grow: 0;
        width: 18px; height: 18px;
        transition: transform 0.2s ease;
        color: var(--bc-text-muted);
    }
    .biz-accordion-chevron.is-open { transform: rotate(180deg); }

    /* ── Sub-header inside collapsible (errors / preview / imported) ── */
    .biz-sub-header { padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--bc-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }

    /* ── Buttons ── */
    .btn-primary  { display:inline-flex; align-items:center; gap:0.4rem; background:#41A2C3; color:#fff; border:none; border-radius:0.45rem; font-size:0.875rem; font-weight:600; padding:0.45rem 0.85rem; cursor:pointer; text-decoration:none; transition:background .12s; }
    .btn-primary:hover  { background:#3290af; }
    .btn-cancel   { display:inline-flex; align-items:center; gap:0.4rem; background:light-dark(#f3f4f6,#09090B); color:var(--bc-text-sec); border:1px solid var(--bc-border); border-radius:0.45rem; font-size:0.875rem; font-weight:600; padding:0.45rem 0.85rem; cursor:pointer; text-decoration:none; transition:background .12s; }
    .btn-cancel:hover   { background:var(--bc-border); }

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
    .biz-tbl td.right { text-align:right; font-variant-numeric:tabular-nums; }

    /* ── Pill badges ── */
    .pill { display:inline-flex; align-items:center; padding:0.18rem 0.55rem; border-radius:999px; font-size:0.72rem; font-weight:600; white-space:nowrap; }
    .pill.new      { background:light-dark(#dbeafe,#1e3a5f); color:light-dark(#1d4ed8,#93c5fd); }
    .pill.update   { background:light-dark(#fef3c7,#2d1f00); color:light-dark(#92400e,#fcd34d); }
    .pill.error    { background:light-dark(#fee2e2,#2d0f0f); color:light-dark(#b91c1c,#fca5a5); }
    .pill.success  { background:light-dark(#dcfce7,#052e16); color:light-dark(#15803d,#86efac); }
    .pill.sheet    { background:light-dark(#f3f4f6,#27272a); color:var(--bc-text-muted); font-size:0.68rem; }

    /* ── Error list inside cell ── */
    .err-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0.25rem; }
    .err-list li::before { content:'• '; color:var(--bc-danger); }
    .err-list li { color:light-dark(#b91c1c,#fca5a5); font-size:0.78rem; }

    /* ── Stats row ── */
    .biz-stats { display:flex; gap:1.5rem; flex-wrap:wrap; }
    .biz-stat  { display:flex; flex-direction:column; gap:0.1rem; }
    .biz-stat-val { font-size:1.5rem; font-weight:700; color:var(--bc-text); font-variant-numeric:tabular-nums; }
    .biz-stat-lbl { font-size:0.78rem; color:var(--bc-text-muted); }

    /* ── Sub-table group label ── */
    .biz-tbl-label { font-size:0.78rem; font-weight:700; color:var(--bc-text-muted); text-transform:uppercase; letter-spacing:0.06em; margin:1rem 0 0.4rem; }
    .biz-tbl-label:first-child { margin-top:0; }
</style>

<div class="biz-import">
<div class="biz-import-wrap" x-data="{ openStep: 1, openMaster: false }">

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- MASTER IMPORT — All sheets in one file (Recommended)                        --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div class="biz-import-card" style="border-color: light-dark(#bfdbfe, #1e3a5f); box-shadow: 0 0 0 1px light-dark(#bfdbfe, #1e3a5f);">

    <button type="button"
            class="biz-accordion-btn"
            :class="{ 'biz-accordion-btn--open': openMaster }"
            @click="openMaster = !openMaster"
            style="background: light-dark(#eff6ff, #0d1a2e);">
        <svg style="width:20px;height:20px;flex-shrink:0;color:#41A2C3;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
        <div style="flex:1; min-width:0;">
            <div style="font-size:1rem; font-weight:700; color:#41A2C3; line-height:1.2;">Master Import &nbsp;·&nbsp; All Tables in One File</div>
            <div style="font-size:0.78rem; color: light-dark(#6b7280, #a1a1aa); margin-top:0.15rem;">Download a single template, fill all 8 sheets, upload once.</div>
        </div>
        <span class="pill" style="background:light-dark(#dbeafe,#1e3a5f); color:light-dark(#1d4ed8,#93c5fd); flex-shrink:0; font-size:0.7rem; padding:0.2rem 0.65rem;">★ All-in-one</span>
        @if($masterState === 'imported')
            <span class="pill success" style="flex-shrink:0;">✓ Imported</span>
        @endif
        <svg class="biz-accordion-chevron" :class="{ 'is-open': openMaster }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="openMaster">

        @if($masterState === 'idle')
        <div class="biz-import-body" style="display:flex; flex-direction:column; gap:1.5rem;">
            <div class="biz-alert info">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                <div>
                    <div style="font-weight:600; margin-bottom:0.3rem;">Single-file workflow</div>
                    <div>The template contains 7 data sheets (Businesses, CostSchemes, CostNodesx, LiabilityStructures, OperativeDocs, Insureds, DocSchemes) plus reference sheets pre-loaded from the database. Fill only the sheets you need, in order. Cross-sheet dropdowns help avoid referential errors. The system validates everything before inserting a single record. Existing records are skipped automatically.</div>
                </div>
            </div>
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">1</span>
                    Download the Master Template
                </div>
                <button wire:click="downloadMasterTemplate" class="btn-primary">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                    Download master_import_template.xlsx
                </button>
            </div>
            <div style="border-top:1px solid var(--bc-border);"></div>
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">2</span>
                    Upload Completed File (.xlsx)
                </div>
                <div class="biz-upload-zone" x-data="{ dragging: false }"
                     @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                     @drop.prevent="dragging=false; $refs.masterFileInput.files=$event.dataTransfer.files; $refs.masterFileInput.dispatchEvent(new Event('change'));"
                     :style="dragging ? 'border-color:#41A2C3;' : ''">
                    <input type="file" x-ref="masterFileInput" wire:model="masterImportFile" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="master-file-input">
                    <label for="master-file-input">
                        <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                        Click to select or drag & drop the master_import_template.xlsx
                    </label>
                    <div class="biz-upload-hint">Only .xlsx files. All sheets are validated before any data is inserted.</div>
                    <div wire:loading wire:target="masterImportFile" style="margin-top:0.6rem; font-size:0.82rem; color:#41A2C3;">Validating all sheets…</div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Errors state ── --}}
        @if($masterState === 'errors')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                <div style="font-size:0.9rem; font-weight:700; color:light-dark(#b91c1c,#fca5a5);">
                    Validation errors in {{ count($masterErrorsBySheet) }} {{ count($masterErrorsBySheet) === 1 ? 'sheet' : 'sheets' }} — no data was inserted
                </div>
            </div>
            <button wire:click="resetMasterState" class="btn-cancel">← Upload New File</button>
        </div>
        <div class="biz-import-body">
            @foreach($masterErrorsBySheet as $sheetName => $sheetErrors)
            <div class="biz-tbl-label">{{ $sheetName }} — {{ count($sheetErrors) }} {{ count($sheetErrors) === 1 ? 'error' : 'errors' }}</div>
            <div class="biz-tbl-wrap" style="margin-bottom:1.5rem;">
                <table class="biz-tbl">
                    <thead><tr><th style="width:5rem;">Row</th><th style="width:18rem;">Key</th><th>Errors</th></tr></thead>
                    <tbody>
                        @foreach($sheetErrors as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['key'] ?? '—' }}</td>
                            <td><ul class="err-list">@foreach($row['errors'] as $err)<li>{{ $err }}</li>@endforeach</ul></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ── Preview state ── --}}
        @if($masterState === 'preview')
        @php
            $masterTotalInsert = array_sum(array_column($masterPreviewCounts, 'insert'));
            $masterTotalSkip   = array_sum(array_column($masterPreviewCounts, 'skipped'));
        @endphp
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">All sheets valid · {{ $masterTotalInsert }} to insert{{ $masterTotalSkip > 0 ? ', ' . $masterTotalSkip . ' already exist (will skip)' : '' }}</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead>
                        <tr>
                            <th>Sheet</th>
                            <th style="text-align:right;">To Insert</th>
                            <th style="text-align:right;">Skip (exists)</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($masterPreviewCounts as $sheet => $counts)
                        @php $sheetTotal = $counts['insert'] + $counts['skipped']; @endphp
                        <tr @if($sheetTotal === 0) style="opacity:.4;" @endif>
                            <td style="font-weight:500;">{{ $sheet }}</td>
                            <td style="text-align:right; font-variant-numeric:tabular-nums;">
                                @if($counts['insert'] > 0)
                                    <span class="pill new">+{{ $counts['insert'] }}</span>
                                @else —
                                @endif
                            </td>
                            <td style="text-align:right; font-variant-numeric:tabular-nums;">
                                @if($counts['skipped'] > 0)
                                    <span class="pill update">→ {{ $counts['skipped'] }}</span>
                                @else —
                                @endif
                            </td>
                            <td style="text-align:right; font-variant-numeric:tabular-nums; font-weight:600;">{{ $sheetTotal ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="biz-alert info" style="margin-top:1rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                <span>Only new records will be inserted. Existing records (by primary key) are skipped silently. All inserts run inside a single database transaction — if anything fails, no data is committed. The batch will be created with status <strong>Pending Review</strong> and must be approved by a manager before it takes effect.</span>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetMasterState" class="btn-cancel">← Cancel</button>
            <button wire:click="confirmMasterImport" wire:loading.attr="disabled" wire:loading.class="opacity-50" class="btn-primary">
                <span wire:loading.remove wire:target="confirmMasterImport">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Submit Import — {{ $masterTotalInsert }} new records
                </span>
                <span wire:loading wire:target="confirmMasterImport">Importing all sheets…</span>
            </button>
        </div>
        @endif

        {{-- ── Imported state ── --}}
        @if($masterState === 'imported')
        @php
            $masterGrandInserted = array_sum(array_column($masterStats, 'inserted'));
            $masterGrandSkipped  = array_sum(array_column($masterStats, 'skipped'));
        @endphp
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">Import submitted · pending review</span>
            </div>
            <span class="pill" style="background:light-dark(#fef3c7,#2d1f00); color:light-dark(#92400e,#fcd34d); font-size:0.72rem;">⏳ Pending Review</span>
        </div>
        <div class="biz-import-body">
            <div class="biz-alert success" style="margin-bottom:1.5rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <div>
                    <div style="font-weight:600; margin-bottom:0.25rem;">Batch <span style="font-family:monospace;">{{ $masterBatchCode }}</span> created</div>
                    <div>{{ $masterGrandInserted }} records inserted{{ $masterGrandSkipped > 0 ? ' · ' . $masterGrandSkipped . ' skipped (already existed)' : '' }}. A manager must approve this batch before the records are considered active.</div>
                </div>
            </div>
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th>Sheet</th><th style="text-align:right;">Inserted</th><th style="text-align:right;">Skipped</th></tr></thead>
                    <tbody>
                        @foreach($masterStats as $sheet => $s)
                        <tr @if($s['inserted'] + $s['skipped'] === 0) style="opacity:.4;" @endif>
                            <td style="font-weight:500;">{{ $sheet }}</td>
                            <td style="text-align:right; color:#16a34a; font-weight:600; font-variant-numeric:tabular-nums;">{{ $s['inserted'] ?: '—' }}</td>
                            <td style="text-align:right; color:var(--bc-text-muted); font-variant-numeric:tabular-nums;">{{ $s['skipped'] ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="biz-alert info" style="margin-top:1rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                <span>Go to <strong>Resources → Import Batches</strong> to track approval status or to approve / reject this batch.</span>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetMasterState" class="btn-cancel">Import Another File</button>
        </div>
        @endif

    </div>{{-- end master collapsible --}}
</div>{{-- end Master Import card --}}

<div style="display:flex; align-items:center; gap:0.75rem; padding:0.25rem 0; margin:0.25rem 0;">
    <div style="flex:1; height:1px; background:var(--bc-border);"></div>
    <span style="font-size:0.72rem; font-weight:600; color:var(--bc-text-muted); letter-spacing:0.08em; text-transform:uppercase; white-space:nowrap;">— or use step-by-step imports below —</span>
    <div style="flex:1; height:1px; background:var(--bc-border);"></div>
</div>

{{-- ════════════════════════════════════════════════════════════════
     STEP 1 — Businesses Table
════════════════════════════════════════════════════════════════ --}}
<div class="biz-import-card">

    {{-- Accordion trigger --}}
    <button type="button" class="biz-accordion-btn" :class="{ 'biz-accordion-btn--open': openStep === 1 }" @click="openStep = 1">
        <svg style="width:20px;height:20px;flex-shrink:0;color:#41A2C3;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <div style="flex:1; min-width:0;">
            <div style="font-size:1rem; font-weight:700; color:#41A2C3; line-height:1.2;">Step 1 &nbsp;·&nbsp; Businesses Table</div>
            <div style="font-size:0.8rem; color:var(--bc-text-muted); margin-top:0.1rem;">Import businesses from Excel</div>
        </div>
        @if($state === 'errors')
            <span class="pill error" style="flex-shrink:0;">✗ {{ count($errorRows) }} error{{ count($errorRows) !== 1 ? 's' : '' }}</span>
        @elseif($state === 'preview')
            <span class="pill new" style="flex-shrink:0;">{{ count($previewRows) }} ready</span>
        @elseif($state === 'imported')
            <span class="pill success" style="flex-shrink:0;">✓ Imported</span>
        @endif
        <svg class="biz-accordion-chevron" :class="{ 'is-open': openStep === 1 }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>

    {{-- Collapsible content --}}
    <div x-show="openStep === 1">

        @if($state === 'idle')
        <div class="biz-import-body" style="display:flex; flex-direction:column; gap:1.5rem;">
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
                    <div wire:loading wire:target="importFile" style="margin-top:0.6rem; font-size:0.82rem; color:#41A2C3;">Validating file…</div>
                </div>
            </div>
        </div>
        @endif

        @if($state === 'errors')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                <div style="font-size:0.9rem; font-weight:700; color:light-dark(#b91c1c,#fca5a5);">{{ count($errorRows) }} validation {{ count($errorRows) === 1 ? 'error' : 'errors' }} — import aborted</div>
            </div>
            <button wire:click="resetState" class="btn-cancel">← Upload New File</button>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th style="width:5rem;">Row</th><th style="width:14rem;">Business Code</th><th>Errors</th></tr></thead>
                    <tbody>
                        @foreach($errorRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['business_code'] ?: '—' }}</td>
                            <td><ul class="err-list">@foreach($row['errors'] as $err)<li>{{ $err }}</li>@endforeach</ul></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($state === 'preview')
        @php $newCount = collect($previewRows)->where('_is_update', false)->count(); $updateCount = collect($previewRows)->where('_is_update', true)->count(); @endphp
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">{{ count($previewRows) }} {{ count($previewRows) === 1 ? 'record' : 'records' }} ready</span>
                <span class="pill new">{{ $newCount }} new</span>
                <span class="pill update">{{ $updateCount }} update</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th>Row</th><th>Business Code</th><th>Description</th><th>Reinsu. Type</th><th>Risk</th><th>Biz. Type</th><th>Premium</th><th>Reinsurer</th><th>Currency</th><th>Region</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($previewRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['business_code'] }}</td>
                            <td style="max-width:18rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['description'] }}">{{ $row['description'] }}</td>
                            <td>{{ $row['reinsurance_type'] }}</td>
                            <td>{{ $row['risk_covered'] }}</td>
                            <td>{{ $row['business_type'] }}</td>
                            <td>{{ $row['premium_type'] }}</td>
                            <td style="max-width:12rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['_reinsurer_name'] }}">{{ $row['_reinsurer_name'] }}</td>
                            <td>{{ $row['_currency_code'] }}</td>
                            <td>{{ $row['_region_name'] }}</td>
                            <td>@if($row['_is_update'])<span class="pill update">Update</span>@else<span class="pill new">New</span>@endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetState" class="btn-cancel">← Cancel</button>
            <button wire:click="confirmImport" wire:loading.attr="disabled" wire:loading.class="opacity-50" class="btn-primary">
                <span wire:loading.remove wire:target="confirmImport"><svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Confirm Import — {{ count($previewRows) }} records</span>
                <span wire:loading wire:target="confirmImport">Importing…</span>
            </button>
        </div>
        @endif

        @if($state === 'imported')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">Import Completed</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-alert success" style="margin-bottom:1.5rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span>{{ $importedCount }} {{ $importedCount === 1 ? 'business' : 'businesses' }} processed successfully.</span>
            </div>
            <div class="biz-stats">
                <div class="biz-stat"><div class="biz-stat-val" style="color:#41A2C3;">{{ $importedCount }}</div><div class="biz-stat-lbl">Total processed</div></div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat"><div class="biz-stat-val" style="color:#16a34a;">{{ $insertedCount }}</div><div class="biz-stat-lbl">New records inserted</div></div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat"><div class="biz-stat-val" style="color:#d97706;">{{ $updatedCount }}</div><div class="biz-stat-lbl">Existing records updated</div></div>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetState" class="btn-cancel">Import Another File</button>
            <a href="{{ \App\Filament\Resources\Businesses\BusinessResource::getUrl('index') }}" class="btn-primary">View Businesses →</a>
        </div>
        @endif

    </div>{{-- end collapsible --}}
</div>{{-- end Step 1 card --}}

{{-- ════════════════════════════════════════════════════════════════
     STEP 2 — Cost Schemes
════════════════════════════════════════════════════════════════ --}}
<div class="biz-import-card">

    {{-- Accordion trigger --}}
    <button type="button" class="biz-accordion-btn" :class="{ 'biz-accordion-btn--open': openStep === 2 }" @click="openStep = 2">
        <svg style="width:20px;height:20px;flex-shrink:0;color:#41A2C3;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
        <div style="flex:1; min-width:0;">
            <div style="font-size:1rem; font-weight:700; color:#41A2C3; line-height:1.2;">Step 2 &nbsp;·&nbsp; Cost Schemes</div>
            <div style="font-size:0.8rem; color:var(--bc-text-muted); margin-top:0.1rem;">Import cost schemes &amp; nodes from Excel</div>
        </div>
        @if($csState === 'errors')
            <span class="pill error" style="flex-shrink:0;">✗ {{ count($csErrorRows) }} error{{ count($csErrorRows) !== 1 ? 's' : '' }}</span>
        @elseif($csState === 'preview')
            <span class="pill new" style="flex-shrink:0;">{{ count($csPreviewSchemes) }} schemes · {{ count($csPreviewNodes) }} nodes</span>
        @elseif($csState === 'imported')
            <span class="pill success" style="flex-shrink:0;">✓ Imported</span>
        @endif
        <svg class="biz-accordion-chevron" :class="{ 'is-open': openStep === 2 }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>

    {{-- Collapsible content --}}
    <div x-show="openStep === 2">

        @if($csState === 'idle')
        <div class="biz-import-body" style="display:flex; flex-direction:column; gap:1.5rem;">
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">1</span>
                    Download the Excel Template
                </div>
                <div class="biz-alert info" style="margin-bottom:0.85rem;">
                    <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <span>The template includes two data sheets: <strong>CostSchemes</strong> and <strong>CostNodesx</strong>, plus reference sheets REF_Deductions, REF_Partners, and README. The <code>scheme_id</code> you enter in CostSchemes is the link key used in CostNodesx.</span>
                </div>
                <button wire:click="downloadCostSchemeTemplate" class="btn-primary">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                    Download Template
                </button>
            </div>
            <div style="border-top:1px solid var(--bc-border);"></div>
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">2</span>
                    Upload Completed File (.xlsx)
                </div>
                <div class="biz-upload-zone" x-data="{ dragging: false }"
                     @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                     @drop.prevent="dragging=false; $refs.csFileInput.files=$event.dataTransfer.files; $refs.csFileInput.dispatchEvent(new Event('change'));"
                     :style="dragging ? 'border-color:#41A2C3;' : ''">
                    <input type="file" x-ref="csFileInput" wire:model="csImportFile" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="cs-file-input">
                    <label for="cs-file-input">
                        <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                        Click to select or drag & drop a .xlsx file
                    </label>
                    <div class="biz-upload-hint">Only .xlsx files. Must use the template format.</div>
                    <div wire:loading wire:target="csImportFile" style="margin-top:0.6rem; font-size:0.82rem; color:#41A2C3;">Validating file…</div>
                </div>
            </div>
        </div>
        @endif

        @if($csState === 'errors')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                <div style="font-size:0.9rem; font-weight:700; color:light-dark(#b91c1c,#fca5a5);">{{ count($csErrorRows) }} validation {{ count($csErrorRows) === 1 ? 'error' : 'errors' }} — import aborted</div>
            </div>
            <button wire:click="resetCostSchemeState" class="btn-cancel">← Upload New File</button>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th style="width:8rem;">Sheet</th><th style="width:5rem;">Row</th><th style="width:14rem;">Key</th><th>Errors</th></tr></thead>
                    <tbody>
                        @foreach($csErrorRows as $row)
                        <tr>
                            <td><span class="pill sheet">{{ $row['sheet'] }}</span></td>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['key'] ?: '—' }}</td>
                            <td><ul class="err-list">@foreach($row['errors'] as $err)<li>{{ $err }}</li>@endforeach</ul></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($csState === 'preview')
        @php
            $csNewSchemes    = collect($csPreviewSchemes)->where('_is_update', false)->count();
            $csUpdateSchemes = collect($csPreviewSchemes)->where('_is_update', true)->count();
            $csNewNodes      = collect($csPreviewNodes)->where('_is_update', false)->count();
            $csUpdateNodes   = collect($csPreviewNodes)->where('_is_update', true)->count();
        @endphp
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">{{ count($csPreviewSchemes) }} schemes · {{ count($csPreviewNodes) }} nodes ready</span>
                <span class="pill new">{{ $csNewSchemes }} scheme{{ $csNewSchemes !== 1 ? 's' : '' }} new</span>
                <span class="pill update">{{ $csUpdateSchemes }} update</span>
                <span class="pill new">{{ $csNewNodes }} node{{ $csNewNodes !== 1 ? 's' : '' }} new</span>
                <span class="pill update">{{ $csUpdateNodes }} update</span>
            </div>
        </div>
        <div class="biz-import-body">
            @if(count($csPreviewSchemes) > 0)
            <div class="biz-tbl-label">Cost Schemes ({{ count($csPreviewSchemes) }})</div>
            <div class="biz-tbl-wrap" style="margin-bottom:1.25rem;">
                <table class="biz-tbl">
                    <thead><tr><th>Row</th><th>Scheme ID</th><th>Index</th><th>Share %</th><th>Agreement Type</th><th style="max-width:22rem;">Description</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($csPreviewSchemes as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['scheme_id'] }}</td>
                            <td class="right">{{ $row['index'] }}</td>
                            <td class="right">{{ number_format($row['share'], 2) }}</td>
                            <td>{{ $row['agreement_type'] }}</td>
                            <td style="max-width:22rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['description'] }}">{{ $row['description'] ?? '—' }}</td>
                            <td>@if($row['_is_update'])<span class="pill update">Update</span>@else<span class="pill new">New</span>@endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            @if(count($csPreviewNodes) > 0)
            <div class="biz-tbl-label">Cost Nodes ({{ count($csPreviewNodes) }})</div>
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th>Row</th><th>Scheme ID</th><th>Index</th><th>Deduction ID</th><th>Value</th><th>Apply Gross</th><th>Partner Source</th><th>Partner Dest.</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($csPreviewNodes as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['cscheme_id'] }}</td>
                            <td class="right">{{ $row['index'] }}</td>
                            <td class="right">{{ $row['concept'] }}</td>
                            <td class="right">{{ number_format($row['value'], 4) }}</td>
                            <td class="center">{{ $row['apply_to_gross'] ? 'Yes' : 'No' }}</td>
                            <td style="max-width:14rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['_partner_source_name'] }}">{{ $row['_partner_source_name'] }}</td>
                            <td style="max-width:14rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['_partner_dest_name'] }}">{{ $row['_partner_dest_name'] }}</td>
                            <td>@if($row['_is_update'])<span class="pill update">Update</span>@else<span class="pill new">New</span>@endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetCostSchemeState" class="btn-cancel">← Cancel</button>
            <button wire:click="confirmCostSchemeImport" wire:loading.attr="disabled" wire:loading.class="opacity-50" class="btn-primary">
                <span wire:loading.remove wire:target="confirmCostSchemeImport"><svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Confirm Import — {{ count($csPreviewSchemes) }} schemes · {{ count($csPreviewNodes) }} nodes</span>
                <span wire:loading wire:target="confirmCostSchemeImport">Importing…</span>
            </button>
        </div>
        @endif

        @if($csState === 'imported')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">Import Completed</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-alert success" style="margin-bottom:1.5rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span>{{ $csImportedSchemes }} {{ $csImportedSchemes === 1 ? 'scheme' : 'schemes' }} and {{ $csImportedNodes }} {{ $csImportedNodes === 1 ? 'node' : 'nodes' }} processed successfully.</span>
            </div>
            <div class="biz-stats">
                <div class="biz-stat"><div class="biz-stat-val" style="color:#41A2C3;">{{ $csImportedSchemes }}</div><div class="biz-stat-lbl">Schemes processed</div></div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat"><div class="biz-stat-val" style="color:#16a34a;">{{ $csInsertedSchemes }}</div><div class="biz-stat-lbl">Schemes inserted</div></div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat"><div class="biz-stat-val" style="color:#d97706;">{{ $csUpdatedSchemes }}</div><div class="biz-stat-lbl">Schemes updated</div></div>
                <div style="width:2px; background:var(--bc-border);"></div>
                <div class="biz-stat"><div class="biz-stat-val" style="color:#41A2C3;">{{ $csImportedNodes }}</div><div class="biz-stat-lbl">Nodes processed</div></div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat"><div class="biz-stat-val" style="color:#16a34a;">{{ $csInsertedNodes }}</div><div class="biz-stat-lbl">Nodes inserted</div></div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat"><div class="biz-stat-val" style="color:#d97706;">{{ $csUpdatedNodes }}</div><div class="biz-stat-lbl">Nodes updated</div></div>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetCostSchemeState" class="btn-cancel">Import Another File</button>
        </div>
        @endif

    </div>{{-- end collapsible --}}
</div>{{-- end Step 2 card --}}

{{-- ════════════════════════════════════════════════════════════════
     STEP 3 — Liability Structures
════════════════════════════════════════════════════════════════ --}}
<div class="biz-import-card">

    {{-- Accordion trigger --}}
    <button type="button" class="biz-accordion-btn" :class="{ 'biz-accordion-btn--open': openStep === 3 }" @click="openStep = 3">
        <svg style="width:20px;height:20px;flex-shrink:0;color:#41A2C3;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <div style="flex:1; min-width:0;">
            <div style="font-size:1rem; font-weight:700; color:#41A2C3; line-height:1.2;">Step 3 &nbsp;·&nbsp; Liability Structures</div>
            <div style="font-size:0.8rem; color:var(--bc-text-muted); margin-top:0.1rem;">Import liability structures from Excel</div>
        </div>
        @if($lsState === 'errors')
            <span class="pill error" style="flex-shrink:0;">✗ {{ count($lsErrorRows) }} error{{ count($lsErrorRows) !== 1 ? 's' : '' }}</span>
        @elseif($lsState === 'preview')
            <span class="pill new" style="flex-shrink:0;">{{ count($lsPreviewRows) }} ready</span>
        @elseif($lsState === 'imported')
            <span class="pill success" style="flex-shrink:0;">✓ Imported</span>
        @endif
        <svg class="biz-accordion-chevron" :class="{ 'is-open': openStep === 3 }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>

    {{-- Collapsible content --}}
    <div x-show="openStep === 3">

        @if($lsState === 'idle')
        <div class="biz-import-body" style="display:flex; flex-direction:column; gap:1.5rem;">
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">1</span>
                    Download the Excel Template
                </div>
                <div class="biz-alert info" style="margin-bottom:0.85rem;">
                    <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <span>The template includes one data sheet: <strong>LiabilityStructures</strong> (business_code, coverage, limits, deductible…). Plus reference sheets REF_Businesses, REF_Coverages, and README. Each row creates a <strong>new</strong> liability structure. The <code>index</code> is assigned automatically.</span>
                </div>
                <button wire:click="downloadLiabilityStructureTemplate" class="btn-primary">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                    Download Template
                </button>
            </div>
            <div style="border-top:1px solid var(--bc-border);"></div>
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">2</span>
                    Upload Completed File (.xlsx)
                </div>
                <div class="biz-upload-zone" x-data="{ dragging: false }"
                     @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                     @drop.prevent="dragging=false; $refs.lsFileInput.files=$event.dataTransfer.files; $refs.lsFileInput.dispatchEvent(new Event('change'));"
                     :style="dragging ? 'border-color:#41A2C3;' : ''">
                    <input type="file" x-ref="lsFileInput" wire:model="lsImportFile" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="ls-file-input">
                    <label for="ls-file-input">
                        <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                        Click to select or drag & drop a .xlsx file
                    </label>
                    <div class="biz-upload-hint">Only .xlsx files. Must use the template format.</div>
                    <div wire:loading wire:target="lsImportFile" style="margin-top:0.6rem; font-size:0.82rem; color:#41A2C3;">Validating file…</div>
                </div>
            </div>
        </div>
        @endif

        @if($lsState === 'errors')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                <div style="font-size:0.9rem; font-weight:700; color:light-dark(#b91c1c,#fca5a5);">{{ count($lsErrorRows) }} validation {{ count($lsErrorRows) === 1 ? 'error' : 'errors' }} — import aborted</div>
            </div>
            <button wire:click="resetLiabilityStructureState" class="btn-cancel">← Upload New File</button>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th style="width:5rem;">Row</th><th style="width:14rem;">Business Code</th><th>Errors</th></tr></thead>
                    <tbody>
                        @foreach($lsErrorRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['business_code'] ?: '—' }}</td>
                            <td><ul class="err-list">@foreach($row['errors'] as $err)<li>{{ $err }}</li>@endforeach</ul></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($lsState === 'preview')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">{{ count($lsPreviewRows) }} {{ count($lsPreviewRows) === 1 ? 'record' : 'records' }} ready — all new</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th>Row</th><th>Business Code</th><th>Coverage</th><th>CLS</th><th>Limit</th><th>Limit Desc.</th><th>Sublimit</th><th>Deductible</th></tr></thead>
                    <tbody>
                        @foreach($lsPreviewRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['business_code'] }}</td>
                            <td style="max-width:14rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['_coverage_name'] }}">{{ $row['_coverage_name'] }}</td>
                            <td class="center">{{ $row['cls'] ? 'Yes' : 'No' }}</td>
                            <td class="right">{{ number_format($row['limit'], 2) }}</td>
                            <td style="max-width:16rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['limit_desc'] }}">{{ $row['limit_desc'] }}</td>
                            <td class="right muted">{{ $row['sublimit'] !== null ? number_format($row['sublimit'], 2) : '—' }}</td>
                            <td class="right muted">{{ $row['deductible'] !== null ? number_format($row['deductible'], 2) : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetLiabilityStructureState" class="btn-cancel">← Cancel</button>
            <button wire:click="confirmLiabilityStructureImport" wire:loading.attr="disabled" wire:loading.class="opacity-50" class="btn-primary">
                <span wire:loading.remove wire:target="confirmLiabilityStructureImport"><svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Confirm Import — {{ count($lsPreviewRows) }} records</span>
                <span wire:loading wire:target="confirmLiabilityStructureImport">Importing…</span>
            </button>
        </div>
        @endif

        @if($lsState === 'imported')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">Import Completed</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-alert success" style="margin-bottom:1.5rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span>{{ $lsInsertedCount }} {{ $lsInsertedCount === 1 ? 'liability structure' : 'liability structures' }} inserted successfully.</span>
            </div>
            <div class="biz-stats">
                <div class="biz-stat"><div class="biz-stat-val" style="color:#16a34a;">{{ $lsInsertedCount }}</div><div class="biz-stat-lbl">Records inserted</div></div>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetLiabilityStructureState" class="btn-cancel">Import Another File</button>
        </div>
        @endif

    </div>{{-- end collapsible --}}
</div>{{-- end Step 3 card --}}

{{-- ════════════════════════════════════════════════════════════════
     STEP 4 — Operative Documents
════════════════════════════════════════════════════════════════ --}}
<div class="biz-import-card">

    {{-- Accordion trigger --}}
    <button type="button" class="biz-accordion-btn" :class="{ 'biz-accordion-btn--open': openStep === 4 }" @click="openStep = 4">
        <svg style="width:20px;height:20px;flex-shrink:0;color:#41A2C3;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <div style="flex:1; min-width:0;">
            <div style="font-size:1rem; font-weight:700; color:#41A2C3; line-height:1.2;">Step 4 &nbsp;·&nbsp; Operative Documents</div>
            <div style="font-size:0.8rem; color:var(--bc-text-muted); margin-top:0.1rem;">Import operative documents from Excel</div>
        </div>
        @if($odState === 'errors')
            <span class="pill error" style="flex-shrink:0;">✗ {{ count($odErrorRows) }} error{{ count($odErrorRows) !== 1 ? 's' : '' }}</span>
        @elseif($odState === 'preview')
            <span class="pill new" style="flex-shrink:0;">{{ count($odPreviewRows) }} ready</span>
        @elseif($odState === 'imported')
            <span class="pill success" style="flex-shrink:0;">✓ Imported</span>
        @endif
        <svg class="biz-accordion-chevron" :class="{ 'is-open': openStep === 4 }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>

    {{-- Collapsible content --}}
    <div x-show="openStep === 4">

        @if($odState === 'idle')
        <div class="biz-import-body" style="display:flex; flex-direction:column; gap:1.5rem;">
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">1</span>
                    Download the Excel Template
                </div>
                <div class="biz-alert info" style="margin-bottom:0.85rem;">
                    <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <span>The template includes one data sheet: <strong>OperativeDocs</strong> (id, business_code, doc type, description, dates, af_mf…). Plus reference sheets REF_Businesses, REF_DocTypes, and README. The <code>id</code> column is the upsert key — existing id → update, new id → insert. The <code>index</code> is assigned automatically.</span>
                </div>
                <button wire:click="downloadOperativeDocTemplate" class="btn-primary">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                    Download Template
                </button>
            </div>
            <div style="border-top:1px solid var(--bc-border);"></div>
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">2</span>
                    Upload Completed File (.xlsx)
                </div>
                <div class="biz-upload-zone" x-data="{ dragging: false }"
                     @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                     @drop.prevent="dragging=false; $refs.odFileInput.files=$event.dataTransfer.files; $refs.odFileInput.dispatchEvent(new Event('change'));"
                     :style="dragging ? 'border-color:#41A2C3;' : ''">
                    <input type="file" x-ref="odFileInput" wire:model="odImportFile" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="od-file-input">
                    <label for="od-file-input">
                        <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                        Click to select or drag & drop a .xlsx file
                    </label>
                    <div class="biz-upload-hint">Only .xlsx files. Must use the template format.</div>
                    <div wire:loading wire:target="odImportFile" style="margin-top:0.6rem; font-size:0.82rem; color:#41A2C3;">Validating file…</div>
                </div>
            </div>
        </div>
        @endif

        @if($odState === 'errors')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                <div style="font-size:0.9rem; font-weight:700; color:light-dark(#b91c1c,#fca5a5);">{{ count($odErrorRows) }} validation {{ count($odErrorRows) === 1 ? 'error' : 'errors' }} — import aborted</div>
            </div>
            <button wire:click="resetOperativeDocState" class="btn-cancel">← Upload New File</button>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th style="width:5rem;">Row</th><th style="width:16rem;">ID</th><th>Errors</th></tr></thead>
                    <tbody>
                        @foreach($odErrorRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['id'] ?: '—' }}</td>
                            <td><ul class="err-list">@foreach($row['errors'] as $err)<li>{{ $err }}</li>@endforeach</ul></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($odState === 'preview')
        @php $odNewCount = collect($odPreviewRows)->where('_is_update', false)->count(); $odUpdateCount = collect($odPreviewRows)->where('_is_update', true)->count(); @endphp
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">{{ count($odPreviewRows) }} {{ count($odPreviewRows) === 1 ? 'record' : 'records' }} ready</span>
                <span class="pill new">{{ $odNewCount }} new</span>
                <span class="pill update">{{ $odUpdateCount }} update</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th>Row</th><th>ID</th><th>Business Code</th><th>Doc Type</th><th>Description</th><th>Inception</th><th>Expiration</th><th>AF/MF</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($odPreviewRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['id'] }}</td>
                            <td class="mono">{{ $row['business_code'] }}</td>
                            <td style="max-width:14rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['_doc_type_name'] }}">{{ $row['_doc_type_name'] }}</td>
                            <td style="max-width:18rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['description'] }}">{{ $row['description'] }}</td>
                            <td class="center">{{ $row['inception_date'] }}</td>
                            <td class="center">{{ $row['expiration_date'] }}</td>
                            <td class="right">{{ $row['af_mf'] !== null ? number_format($row['af_mf'], 6) : '—' }}</td>
                            <td>@if($row['_is_update'])<span class="pill update">Update</span>@else<span class="pill new">New</span>@endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetOperativeDocState" class="btn-cancel">← Cancel</button>
            <button wire:click="confirmOperativeDocImport" wire:loading.attr="disabled" wire:loading.class="opacity-50" class="btn-primary">
                <span wire:loading.remove wire:target="confirmOperativeDocImport"><svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Confirm Import — {{ count($odPreviewRows) }} records</span>
                <span wire:loading wire:target="confirmOperativeDocImport">Importing…</span>
            </button>
        </div>
        @endif

        @if($odState === 'imported')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">Import Completed</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-alert success" style="margin-bottom:1.5rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span>{{ $odInsertedCount + $odUpdatedCount }} operative {{ ($odInsertedCount + $odUpdatedCount) === 1 ? 'document' : 'documents' }} processed successfully.</span>
            </div>
            <div class="biz-stats">
                <div class="biz-stat"><div class="biz-stat-val" style="color:#41A2C3;">{{ $odInsertedCount + $odUpdatedCount }}</div><div class="biz-stat-lbl">Total processed</div></div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat"><div class="biz-stat-val" style="color:#16a34a;">{{ $odInsertedCount }}</div><div class="biz-stat-lbl">New records inserted</div></div>
                <div style="width:1px; background:var(--bc-border);"></div>
                <div class="biz-stat"><div class="biz-stat-val" style="color:#d97706;">{{ $odUpdatedCount }}</div><div class="biz-stat-lbl">Existing records updated</div></div>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetOperativeDocState" class="btn-cancel">Import Another File</button>
        </div>
        @endif

    </div>{{-- end collapsible --}}
</div>{{-- end Step 4 card --}}

{{-- ════════════════════════════════════════════════════════════════
     STEP 5 — Insureds
════════════════════════════════════════════════════════════════ --}}
<div class="biz-import-card">

    {{-- Accordion trigger --}}
    <button type="button" class="biz-accordion-btn" :class="{ 'biz-accordion-btn--open': openStep === 5 }" @click="openStep = 5">
        <svg style="width:20px;height:20px;flex-shrink:0;color:#41A2C3;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <div style="flex:1; min-width:0;">
            <div style="font-size:1rem; font-weight:700; color:#41A2C3; line-height:1.2;">Step 5 &nbsp;·&nbsp; Insureds</div>
            <div style="font-size:0.8rem; color:var(--bc-text-muted); margin-top:0.1rem;">Import insured premium allocations from Excel</div>
        </div>
        @if($biState === 'errors')
            <span class="pill error" style="flex-shrink:0;">✗ {{ count($biErrorRows) }} error{{ count($biErrorRows) !== 1 ? 's' : '' }}</span>
        @elseif($biState === 'preview')
            <span class="pill new" style="flex-shrink:0;">{{ count($biPreviewRows) }} ready</span>
        @elseif($biState === 'imported')
            <span class="pill success" style="flex-shrink:0;">✓ Imported</span>
        @endif
        <svg class="biz-accordion-chevron" :class="{ 'is-open': openStep === 5 }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>

    {{-- Collapsible content --}}
    <div x-show="openStep === 5">

        @if($biState === 'idle')
        <div class="biz-import-body" style="display:flex; flex-direction:column; gap:1.5rem;">
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">1</span>
                    Download the Excel Template
                </div>
                <div class="biz-alert info" style="margin-bottom:0.85rem;">
                    <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <span>The template includes one data sheet: <strong>Insureds</strong> (op_document_id, cscheme_id, company, coverage, premium). Plus reference sheets REF_OperativeDocs, REF_CostSchemes, REF_Companies, REF_Coverages, and README. Each row creates a <strong>new</strong> insured record. The <code>id</code> (UUID) is assigned automatically.</span>
                </div>
                <button wire:click="downloadInsuredTemplate" class="btn-primary">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                    Download Template
                </button>
            </div>
            <div style="border-top:1px solid var(--bc-border);"></div>
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">2</span>
                    Upload Completed File (.xlsx)
                </div>
                <div class="biz-upload-zone" x-data="{ dragging: false }"
                     @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                     @drop.prevent="dragging=false; $refs.biFileInput.files=$event.dataTransfer.files; $refs.biFileInput.dispatchEvent(new Event('change'));"
                     :style="dragging ? 'border-color:#41A2C3;' : ''">
                    <input type="file" x-ref="biFileInput" wire:model="biImportFile" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="bi-file-input">
                    <label for="bi-file-input">
                        <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                        Click to select or drag & drop a .xlsx file
                    </label>
                    <div class="biz-upload-hint">Only .xlsx files. Must use the template format.</div>
                    <div wire:loading wire:target="biImportFile" style="margin-top:0.6rem; font-size:0.82rem; color:#41A2C3;">Validating file…</div>
                </div>
            </div>
        </div>
        @endif

        @if($biState === 'errors')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                <div style="font-size:0.9rem; font-weight:700; color:light-dark(#b91c1c,#fca5a5);">{{ count($biErrorRows) }} validation {{ count($biErrorRows) === 1 ? 'error' : 'errors' }} — import aborted</div>
            </div>
            <button wire:click="resetInsuredState" class="btn-cancel">← Upload New File</button>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th style="width:5rem;">Row</th><th style="width:16rem;">Op. Document ID</th><th>Errors</th></tr></thead>
                    <tbody>
                        @foreach($biErrorRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['op_document_id'] ?: '—' }}</td>
                            <td><ul class="err-list">@foreach($row['errors'] as $err)<li>{{ $err }}</li>@endforeach</ul></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($biState === 'preview')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">{{ count($biPreviewRows) }} {{ count($biPreviewRows) === 1 ? 'record' : 'records' }} ready — all new</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th>Row</th><th>Op. Document ID</th><th>Scheme ID</th><th>Company</th><th>Coverage</th><th>Premium</th></tr></thead>
                    <tbody>
                        @foreach($biPreviewRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['op_document_id'] }}</td>
                            <td class="mono">{{ $row['cscheme_id'] }}</td>
                            <td style="max-width:16rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['_company_name'] }}">{{ $row['_company_name'] }}</td>
                            <td style="max-width:14rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['_coverage_name'] }}">{{ $row['_coverage_name'] }}</td>
                            <td class="right">{{ number_format($row['premium'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetInsuredState" class="btn-cancel">← Cancel</button>
            <button wire:click="confirmInsuredImport" wire:loading.attr="disabled" wire:loading.class="opacity-50" class="btn-primary">
                <span wire:loading.remove wire:target="confirmInsuredImport"><svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Confirm Import — {{ count($biPreviewRows) }} records</span>
                <span wire:loading wire:target="confirmInsuredImport">Importing…</span>
            </button>
        </div>
        @endif

        @if($biState === 'imported')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">Import Completed</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-alert success" style="margin-bottom:1.5rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span>{{ $biInsertedCount }} insured {{ $biInsertedCount === 1 ? 'record' : 'records' }} inserted successfully.</span>
            </div>
            <div class="biz-stats">
                <div class="biz-stat"><div class="biz-stat-val" style="color:#16a34a;">{{ $biInsertedCount }}</div><div class="biz-stat-lbl">Records inserted</div></div>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetInsuredState" class="btn-cancel">Import Another File</button>
        </div>
        @endif

    </div>{{-- end collapsible --}}
</div>{{-- end Step 5 card --}}

{{-- ════════════════════════════════════════════════════════════════
     STEP 6 — Document Cost Schemes
════════════════════════════════════════════════════════════════ --}}
<div class="biz-import-card">

    {{-- Accordion trigger --}}
    <button type="button" class="biz-accordion-btn" :class="{ 'biz-accordion-btn--open': openStep === 6 }" @click="openStep = 6">
        <svg style="width:20px;height:20px;flex-shrink:0;color:#41A2C3;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        <div style="flex:1; min-width:0;">
            <div style="font-size:1rem; font-weight:700; color:#41A2C3; line-height:1.2;">Step 6 &nbsp;·&nbsp; Documents Cost Schemes</div>
            <div style="font-size:0.8rem; color:var(--bc-text-muted); margin-top:0.1rem;">Link operative documents to cost schemes</div>
        </div>
        @if($dsState === 'errors')
            <span class="pill error" style="flex-shrink:0;">✗ {{ count($dsErrorRows) }} error{{ count($dsErrorRows) !== 1 ? 's' : '' }}</span>
        @elseif($dsState === 'preview')
            <span class="pill new" style="flex-shrink:0;">{{ count($dsPreviewRows) }} ready</span>
        @elseif($dsState === 'imported')
            <span class="pill success" style="flex-shrink:0;">✓ Imported</span>
        @endif
        <svg class="biz-accordion-chevron" :class="{ 'is-open': openStep === 6 }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>

    {{-- Collapsible content --}}
    <div x-show="openStep === 6">

        @if($dsState === 'idle')
        <div class="biz-import-body" style="display:flex; flex-direction:column; gap:1.5rem;">
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">1</span>
                    Download the Excel Template
                </div>
                <div class="biz-alert info" style="margin-bottom:0.85rem;">
                    <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <span>The template includes one data sheet: <strong>DocSchemes</strong> with only two columns — <code>op_document_id</code> and <code>cscheme_id</code>. Plus reference sheets REF_OperativeDocs, REF_CostSchemes, and README. The <code>id</code> (UUID) and <code>index</code> are assigned automatically.</span>
                </div>
                <button wire:click="downloadDocSchemeTemplate" class="btn-primary">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                    Download Template
                </button>
            </div>
            <div style="border-top:1px solid var(--bc-border);"></div>
            <div>
                <div class="biz-section-title">
                    <span class="biz-step-badge active">2</span>
                    Upload Completed File (.xlsx)
                </div>
                <div class="biz-upload-zone" x-data="{ dragging: false }"
                     @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                     @drop.prevent="dragging=false; $refs.dsFileInput.files=$event.dataTransfer.files; $refs.dsFileInput.dispatchEvent(new Event('change'));"
                     :style="dragging ? 'border-color:#41A2C3;' : ''">
                    <input type="file" x-ref="dsFileInput" wire:model="dsImportFile" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="ds-file-input">
                    <label for="ds-file-input">
                        <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                        Click to select or drag & drop a .xlsx file
                    </label>
                    <div class="biz-upload-hint">Only .xlsx files. Must use the template format.</div>
                    <div wire:loading wire:target="dsImportFile" style="margin-top:0.6rem; font-size:0.82rem; color:#41A2C3;">Validating file…</div>
                </div>
            </div>
        </div>
        @endif

        @if($dsState === 'errors')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                <div style="font-size:0.9rem; font-weight:700; color:light-dark(#b91c1c,#fca5a5);">{{ count($dsErrorRows) }} validation {{ count($dsErrorRows) === 1 ? 'error' : 'errors' }} — import aborted</div>
            </div>
            <button wire:click="resetDocSchemeState" class="btn-cancel">← Upload New File</button>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th style="width:5rem;">Row</th><th style="width:16rem;">Op. Document ID</th><th>Errors</th></tr></thead>
                    <tbody>
                        @foreach($dsErrorRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['op_document_id'] ?: '—' }}</td>
                            <td><ul class="err-list">@foreach($row['errors'] as $err)<li>{{ $err }}</li>@endforeach</ul></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($dsState === 'preview')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">{{ count($dsPreviewRows) }} {{ count($dsPreviewRows) === 1 ? 'record' : 'records' }} ready — all new</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-tbl-wrap">
                <table class="biz-tbl">
                    <thead><tr><th>Row</th><th>Op. Document ID</th><th>Scheme ID</th></tr></thead>
                    <tbody>
                        @foreach($dsPreviewRows as $row)
                        <tr>
                            <td class="center muted">{{ $row['row'] }}</td>
                            <td class="mono">{{ $row['op_document_id'] }}</td>
                            <td class="mono">{{ $row['cscheme_id'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetDocSchemeState" class="btn-cancel">← Cancel</button>
            <button wire:click="confirmDocSchemeImport" wire:loading.attr="disabled" wire:loading.class="opacity-50" class="btn-primary">
                <span wire:loading.remove wire:target="confirmDocSchemeImport"><svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Confirm Import — {{ count($dsPreviewRows) }} records</span>
                <span wire:loading wire:target="confirmDocSchemeImport">Importing…</span>
            </button>
        </div>
        @endif

        @if($dsState === 'imported')
        <div class="biz-sub-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span style="font-size:0.9rem; font-weight:700; color:var(--bc-text);">Import Completed</span>
            </div>
        </div>
        <div class="biz-import-body">
            <div class="biz-alert success" style="margin-bottom:1.5rem;">
                <svg style="flex-shrink:0;width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <span>{{ $dsInsertedCount }} document cost scheme {{ $dsInsertedCount === 1 ? 'record' : 'records' }} inserted successfully.</span>
            </div>
            <div class="biz-stats">
                <div class="biz-stat"><div class="biz-stat-val" style="color:#16a34a;">{{ $dsInsertedCount }}</div><div class="biz-stat-lbl">Records inserted</div></div>
            </div>
        </div>
        <div class="biz-import-footer">
            <button wire:click="resetDocSchemeState" class="btn-cancel">Import Another File</button>
        </div>
        @endif

    </div>{{-- end collapsible --}}
</div>{{-- end Step 6 card --}}


</div>
</div>

</x-filament-panels::page>
