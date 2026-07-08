<x-filament-panels::page>

    @php
        $months = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];
        $monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $yr = $this->year;
    @endphp

    <style>
        /* ── Semantic tokens — usa variables nativas de Filament v4 / Tailwind v4 ── */
        .batch-card-wrap {
            --bc-card:           light-dark(var(--color-white),       #18181B);
            --bc-surface:        light-dark(var(--color-gray-50),     var(--color-gray-900));
            --bc-sunken:         light-dark(var(--color-gray-100),    var(--color-gray-700));
            --bc-border:         light-dark(var(--color-gray-200),    var(--color-gray-700));
            --bc-border-input:   light-dark(var(--color-gray-300),    var(--color-gray-600));
            --bc-text:           light-dark(var(--color-gray-950),    var(--color-white));
            --bc-text-sec:       light-dark(var(--color-gray-700),    var(--color-gray-200));
            --bc-text-muted:     light-dark(var(--color-gray-500),    var(--color-gray-400));
            --bc-text-subtle:    light-dark(var(--color-gray-400),    var(--color-gray-600));
            --bc-input-bg:       light-dark(var(--color-white),       #252527);
            --bc-row-hover:      light-dark(var(--color-gray-50),     var(--color-gray-700));
            --bc-row-stripe:     light-dark(var(--color-gray-50),     var(--color-gray-800));
            --bc-primary:        light-dark(var(--color-primary-600), var(--color-primary-500));
            --bc-primary-hover:  light-dark(var(--color-primary-700), var(--color-primary-400));
            --bc-primary-subtle: light-dark(var(--color-primary-50),  var(--color-primary-950));
            --bc-primary-text:   light-dark(var(--color-primary-700), var(--color-primary-300));
            --bc-primary-border: light-dark(var(--color-primary-200), var(--color-primary-800));
            --bc-success:        light-dark(var(--color-success-600), var(--color-success-500));
            --bc-danger:         light-dark(var(--color-danger-600),  var(--color-danger-400));
        }

        /* ── Card shell ───────────────────────────────────────── */
        .batch-card {
            background: var(--bc-card);
            border: 1px solid var(--bc-border);
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .batch-toolbar {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--bc-border);
            display: flex; flex-wrap: wrap; align-items: center; gap: 1rem;
        }
        .batch-toolbar-label {
            font-size: 0.70rem; font-weight: 600;
            letter-spacing: 0.07em; text-transform: uppercase;
            color: var(--bc-text-muted); margin-bottom: 0.3rem;
        }

        /* ── Form controls ────────────────────────────────────── */
        .batch-ctrl select,
        .batch-ctrl input[type="text"] {
            background: var(--bc-input-bg);
            border: 1px solid var(--bc-border-input);
            border-radius: 0.5rem;
            color: var(--bc-text);
            font-size: 0.875rem;
            line-height: 1.5;
            padding: 0.45rem 0.75rem;
            outline: none;
            transition: border-color 0.15s;
            box-sizing: border-box;
        }
        .batch-ctrl select {
            appearance: none; -webkit-appearance: none;
            padding-right: 2rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.6rem center;
        }
        .batch-ctrl select:focus,
        .batch-ctrl input[type="text"]:focus {
            border-color: var(--bc-primary);
            box-shadow: 0 0 0 3px rgb(var(--fi-color-primary-600) / 0.12);
        }
        .dark .batch-ctrl select:focus,
        .dark .batch-ctrl input[type="text"]:focus {
            box-shadow: 0 0 0 3px rgb(var(--fi-color-primary-500) / 0.18);
        }
        .batch-ctrl-year select  { width: 7rem; }
        .batch-ctrl-label input  { width: 22rem; }

        .batch-notes textarea {
            background: var(--bc-input-bg);
            border: 1px solid var(--bc-border-input);
            border-radius: 0.5rem;
            color: var(--bc-text);
            font-size: 0.875rem;
            padding: 0.45rem 0.75rem;
            resize: vertical; width: 100%; outline: none;
            transition: border-color 0.15s;
        }
        .batch-notes textarea:focus {
            border-color: var(--bc-primary);
            box-shadow: 0 0 0 3px rgb(var(--fi-color-primary-600) / 0.12);
        }
        .dark .batch-notes textarea:focus {
            box-shadow: 0 0 0 3px rgb(var(--fi-color-primary-500) / 0.18);
        }

        .batch-version-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            background: #41A2C3; color: #fff;
            border-radius: 0.45rem; font-size: 0.875rem; font-weight: 600;
            padding: 0.45rem 0.85rem;
            border: none;
        }

        /* ── Data table ───────────────────────────────────────── */
        .batch-table-wrap { overflow-x: auto; }
        .batch-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }

        .batch-table thead tr { background: light-dark(var(--color-gray-50), #1D1D20); }
        .batch-table th {
            padding: 0.55rem 0.5rem; text-align: center;
            font-size: 0.82rem; font-weight: 600;
            color: light-dark(var(--color-gray-700), var(--color-gray-200));
            border-bottom: 1px solid var(--bc-border);
            white-space: nowrap;
        }
        .batch-table th.col-check  { width: 2.5rem; }
        .batch-table th.col-id     { width: 3rem; text-align: right; }
        .batch-table th.col-name   { text-align: left; min-width: 10rem; padding-left: 0.75rem; }
        .batch-table th.col-py     { text-align: right; min-width: 6.5rem; }
        .batch-table th.col-month  { width: 8rem; }
        .batch-table th.col-total  { text-align: right; min-width: 6.5rem;
                                     background: var(--bc-primary-subtle); color: var(--bc-primary-text); }
        .batch-table th.col-delta  { text-align: right; min-width: 5rem; }

        .batch-table tbody tr {
            border-bottom: 1px solid var(--bc-border);
            transition: background 0.1s;
        }
        .batch-table tbody tr:hover      { background: var(--bc-row-hover); }
        .batch-table tbody tr.row-excluded { opacity: 0.45; }

        .batch-table td { padding: 0.45rem 0.5rem; color: var(--bc-text-sec); vertical-align: middle; }
        .batch-table td.col-check { text-align: center; }
        .batch-table td.col-id    { text-align: right; color: var(--bc-text-muted);
                                    font-size: 0.75rem; font-variant-numeric: tabular-nums; }
        .batch-table td.col-name  { font-weight: 500; color: var(--bc-text); padding-left: 0.75rem; }
        .batch-table td.col-py    { text-align: right; color: var(--bc-text-muted); font-variant-numeric: tabular-nums; }
        .batch-table td.col-month { text-align: center; }
        .batch-table td.col-total { text-align: right; font-variant-numeric: tabular-nums; font-weight: 600;
                                    background: var(--bc-primary-subtle); color: var(--bc-primary-text); }
        .batch-table td.col-delta { text-align: right; }

        /* ── Month inputs ─────────────────────────────────────── */
        .month-input {
            background: var(--bc-input-bg);
            border: 1px solid var(--bc-border);
            border-radius: 0.35rem;
            color: var(--bc-text);
            font-size: 0.8rem; font-variant-numeric: tabular-nums;
            padding: 0.3rem 0.4rem; text-align: right; width: 7.25rem;
            outline: none; transition: border-color 0.15s;
        }
        .month-input:focus {
            border-color: var(--bc-primary);
            box-shadow: 0 0 0 2px rgb(var(--fi-color-primary-600) / 0.12);
        }
        .dark .month-input:focus {
            box-shadow: 0 0 0 2px rgb(var(--fi-color-primary-500) / 0.18);
        }
        .month-input:disabled { opacity: 0.35; cursor: not-allowed; }

        .row-check {
            width: 1.05rem; height: 1.05rem; cursor: pointer;
            appearance: none; -webkit-appearance: none;
            border: 2px solid var(--bc-border-input);
            border-radius: 0.25rem;
            background: var(--bc-input-bg);
            position: relative;
            transition: background 0.12s, border-color 0.12s;
            flex-shrink: 0;
        }
        .row-check:checked {
            background: #41A2C3;
            border-color: #41A2C3;
        }
        .row-check:checked::after {
            content: '';
            position: absolute;
            left: 50%; top: 42%;
            transform: translate(-50%, -50%) rotate(45deg);
            width: 0.28rem; height: 0.52rem;
            border: 2px solid #fff;
            border-top: none; border-left: none;
        }

        /* ── Delta badges ─────────────────────────────────────── */
        .delta-up   { color: var(--bc-success); font-weight: 600; font-size: 0.75rem; }
        .delta-down { color: var(--bc-danger);  font-weight: 600; font-size: 0.75rem; }
        .delta-flat { color: var(--bc-text-muted); font-size: 0.75rem; }

        /* ── Footer ───────────────────────────────────────────── */
        .batch-footer {
            padding: 1rem 1.25rem; border-top: 1px solid var(--bc-border);
            display: flex; align-items: center;
            justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;
        }
        .batch-footer-info { font-size: 0.80rem; color: var(--bc-text-muted); }
        .batch-footer-info span { font-weight: 700; color: var(--bc-text-sec); }

        /* ── Buttons ──────────────────────────────────────────── */
        .btn-save {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: #41A2C3; color: #fff; border: none;
            border-radius: 0.45rem; font-size: 0.875rem; font-weight: 600;
            padding: 0.45rem 0.85rem; cursor: pointer;
            transition: background 0.12s, transform 0.1s;
        }
        .btn-save:hover  { background: #3290af; }
        .btn-save:active { transform: scale(0.98); }

        .btn-cancel {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: light-dark(var(--color-gray-100), #09090B); color: var(--bc-text-sec);
            border: 1px solid var(--bc-border-input);
            border-radius: 0.45rem; font-size: 0.875rem; font-weight: 600;
            padding: 0.45rem 0.85rem; cursor: pointer; text-decoration: none;
            transition: background 0.12s;
        }
        .btn-cancel:hover { background: var(--bc-border); }

        .notes-section {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--bc-border);
        }
    </style>

    <div class="batch-card batch-card-wrap">

        {{-- Toolbar ─────────────────────────────────────────── --}}
        <div class="batch-toolbar">
            <div class="batch-ctrl batch-ctrl-year" style="display:flex; align-items:center; gap:0.5rem;">
                <label style="font-size:0.875rem; font-weight:500; color:var(--bc-text-sec); white-space:nowrap;">Year</label>
                <select wire:model.live="year">
                    @foreach(range(now()->year - 2, now()->year + 2) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="batch-ctrl batch-ctrl-label" style="flex:1; min-width:14rem; display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                <label style="font-size:0.875rem; font-weight:500; color:var(--bc-text-sec); white-space:nowrap;">
                    Version Label <span style="color:var(--bc-danger)">*</span>
                </label>
                <div style="flex:1; min-width:10rem;">
                    <input type="text"
                           wire:model="versionLabel"
                           placeholder="e.g. Initial Budget, Q2 Revision…"
                           style="width:100%">
                    @error('versionLabel')
                        <div style="color:var(--bc-danger); font-size:0.75rem; margin-top:0.2rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:0.5rem;">
                <label style="font-size:0.875rem; font-weight:500; color:var(--bc-text-sec); white-space:nowrap;">Will Save As</label>
                <div class="batch-version-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                    v{{ $this->nextVersion() }}
                </div>
            </div>
        </div>

        {{-- Import / Export strip ──────────────────────────── --}}
        <div style="position:relative; background:var(--bc-surface);">

            {{-- Indeterminate progress bar --}}
            <div wire:loading wire:target="importFromFile"
                 style="position:absolute; bottom:0; left:0; right:0; height:2px; overflow:hidden; z-index:10;">
                <div style="height:100%; width:40%; background:linear-gradient(90deg,transparent,#41A2C3,transparent);
                            animation:import-slide 1.2s ease-in-out infinite;"></div>
            </div>

            <div style="padding:0.65rem 1.25rem; border-bottom:1px solid var(--bc-border);
                        display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">

                {{-- Download template --}}
                <button wire:click="downloadTemplate" wire:loading.attr="disabled" wire:target="downloadTemplate"
                        class="btn-save">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span wire:loading.remove wire:target="downloadTemplate">Download Template</span>
                    <span wire:loading wire:target="downloadTemplate">Generating…</span>
                </button>

                <div style="width:1px; height:1.5rem; background:var(--bc-border); flex-shrink:0;"></div>

                {{-- File input + Import button --}}
                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                    <label style="font-size:0.875rem; font-weight:500; color:var(--bc-text-sec); white-space:nowrap;">
                        Import:
                    </label>
                    <input type="file"
                           wire:model="importFile"
                           accept=".xlsx,.xls,.csv"
                           style="font-size:0.875rem; color:var(--bc-text-sec);
                                  background:var(--bc-input-bg);
                                  border:1px solid var(--bc-border-input);
                                  border-radius:0.45rem; padding:0.45rem 0.75rem; cursor:pointer;">
                    @error('importFile')
                        <span style="color:var(--bc-danger); font-size:0.75rem;">{{ $message }}</span>
                    @enderror
                    <button wire:click="importFromFile"
                            wire:loading.attr="disabled"
                            wire:target="importFromFile,importFile"
                            style="display:inline-flex; align-items:center; gap:0.4rem;
                                   background:#41A2C3; color:#ffffff;
                                   border:none; border-radius:0.45rem;
                                   font-size:0.875rem; font-weight:600;
                                   padding:0.45rem 0.85rem; cursor:pointer; transition:background .12s;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"
                                style="transform:scaleY(-1); transform-origin:center"/>
                            <path d="M3 9l9-9 9 9"/><line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        <span wire:loading.remove wire:target="importFromFile">Load File</span>
                        <span wire:loading wire:target="importFromFile">Importing…</span>
                    </button>
                </div>

                <span style="font-size:0.82rem; color:light-dark(var(--color-gray-500),var(--color-gray-200)); margin-left:auto;">
                    Accepted: .xlsx, .xls, .csv &nbsp;·&nbsp; Do not modify columns ID or CNS in the template
                </span>
            </div>
        </div>{{-- /import-export wrapper --}}

        {{-- Notes ──────────────────────────────────────────── --}}
        <div class="notes-section">
            <div class="batch-toolbar-label" style="margin-bottom:0.35rem; text-transform:none; letter-spacing:normal; color:var(--bc-text); font-size:0.875rem; font-weight:400;">Internal notes (Optional)</div>
            <div class="batch-notes">
                <textarea wire:model="notes" rows="2" placeholder="Reason for revision, assumptions applied…"></textarea>
            </div>
        </div>

        {{-- Grid ───────────────────────────────────────────── --}}
        <div class="batch-table-wrap">
            <table class="batch-table" id="budget-table">
                <thead>
                    <tr>
                        <th class="col-check">
                            <input type="checkbox" class="row-check" id="chk-all" onchange="toggleAll(this)">
                        </th>
                        <th class="col-id">#</th>
                        <th class="col-name">Reinsurer</th>
                        <th class="col-py">Prev. Total</th>
                        @foreach($monthLabels as $i => $label)
                            <th class="col-month">
                                <span style="font-size:0.72rem; display:block; color:var(--bc-text-subtle);">
                                    {{ sprintf('%04d%02d', $yr, $i + 1) }}
                                </span>
                                {{ $label }}
                            </th>
                        @endforeach
                        <th class="col-total">Total</th>
                        <th class="col-delta">Change</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $id => $row)
                        @php
                            $pyVal   = $row['py_budget'];
                            $hasPrev = $pyVal !== null;
                            $newTotal = array_sum(array_map(
                                fn($m) => (float) str_replace(',', '', $row[$m] ?? '0'),
                                $months
                            ));
                            if ($hasPrev && $pyVal > 0) {
                                $deltaPct   = (($newTotal - $pyVal) / $pyVal) * 100;
                                $deltaClass = $deltaPct > 0 ? 'delta-up' : ($deltaPct < 0 ? 'delta-down' : 'delta-flat');
                                $deltaLabel = ($deltaPct >= 0 ? '+' : '') . number_format($deltaPct, 1) . '%';
                            } else {
                                $deltaClass = 'delta-flat';
                                $deltaLabel = $hasPrev ? '—' : 'New';
                            }
                        @endphp
                        <tr data-row-id="{{ $id }}"
                            data-py="{{ $pyVal ?? 0 }}"
                            class="{{ ! $row['included'] ? 'row-excluded' : '' }}">
                            <td class="col-check">
                                <input type="checkbox"
                                       class="row-check"
                                       wire:model.live="rows.{{ $id }}.included">
                            </td>
                            <td class="col-id">{{ $id }}</td>
                            <td class="col-name">{{ $row['name'] }}</td>
                            <td class="col-py">
                                @if($hasPrev)
                                    ${{ number_format($pyVal, 2) }}
                                @else
                                    <span style="color:var(--bc-text-subtle)">—</span>
                                @endif
                            </td>
                            @foreach($months as $mk)
                            <td class="col-month">
                                <input type="text"
                                       class="month-input"
                                       data-month="{{ $mk }}"
                                       wire:model.defer="rows.{{ $id }}.{{ $mk }}"
                                       {{ ! $row['included'] ? 'disabled' : '' }}>
                            </td>
                            @endforeach
                            <td class="col-total">
                                <span class="row-total-val">${{ number_format($newTotal, 2) }}</span>
                            </td>
                            <td class="col-delta">
                                <span class="row-change-val {{ $deltaClass }}">{{ $deltaLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + count($months) + 2 }}"
                                style="text-align:center; padding:2rem; color:var(--bc-text-muted)">
                                No active reinsurers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer ─────────────────────────────────────────── --}}
        <div class="batch-footer">
            <div class="batch-footer-info">
                @php
                    $included = collect($rows)->filter(fn($r) => $r['included'])->count();
                    $total    = count($rows);
                    $grandSum = collect($rows)->filter(fn($r) => $r['included'])->sum(function($r) use ($months) {
                        return array_sum(array_map(fn($m) => (float) str_replace(',', '', $r[$m] ?? '0'), $months));
                    });
                @endphp
                <span>{{ $included }}</span> of {{ $total }} reinsurers &nbsp;·&nbsp;
                Total: <span id="footer-grand-total">${{ number_format($grandSum, 2) }}</span>
            </div>
            <div style="display:flex; gap:0.6rem;">
                <a href="{{ \App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource::getUrl('index') }}"
                   class="btn-cancel">Cancel</a>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-save">
                    <span wire:loading.remove wire:target="save">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    </span>
                    <span wire:loading wire:target="save">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    </span>
                    Save Version {{ $this->nextVersion() }}
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes import-slide { 0% { transform: translateX(-150%); } 100% { transform: translateX(350%); } }
    </style>

    <script>
    (function () {
        function parseVal(s) {
            return parseFloat(String(s).replace(/,/g, '')) || 0;
        }
        function money(n) {
            return '$' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function recalcRow(tr) {
            let total = 0;
            tr.querySelectorAll('.month-input').forEach(function (inp) {
                total += parseVal(inp.value);
            });

            var totalEl = tr.querySelector('.row-total-val');
            if (totalEl) totalEl.textContent = money(total);

            var changeEl = tr.querySelector('.row-change-val');
            if (changeEl) {
                var py = parseFloat(tr.dataset.py || '0');
                var excluded = tr.classList.contains('row-excluded');
                changeEl.className = 'row-change-val';
                if (excluded) {
                    changeEl.textContent = '';
                } else if (py > 0) {
                    var pct = ((total - py) / py) * 100;
                    changeEl.classList.add(pct > 0 ? 'delta-up' : pct < 0 ? 'delta-down' : 'delta-flat');
                    changeEl.textContent = (pct >= 0 ? '+' : '') + pct.toFixed(1) + '%';
                } else if (total > 0) {
                    changeEl.classList.add('delta-flat');
                    changeEl.textContent = 'New';
                } else {
                    changeEl.classList.add('delta-flat');
                    changeEl.textContent = '—';
                }
            }
            return total;
        }

        function recalcFooter() {
            var grand = 0;
            document.querySelectorAll('#budget-table tbody tr[data-row-id]').forEach(function (tr) {
                if (!tr.classList.contains('row-excluded')) {
                    var el = tr.querySelector('.row-total-val');
                    if (el) grand += parseVal(el.textContent.replace('$', '').replace(/,/g, ''));
                }
            });
            var el = document.getElementById('footer-grand-total');
            if (el) el.textContent = money(grand);
        }

        function recalcAll() {
            document.querySelectorAll('#budget-table tbody tr[data-row-id]').forEach(recalcRow);
            recalcFooter();
        }

        function formatInput(inp) {
            var raw = String(inp.value).replace(/,/g, '');
            var num = parseFloat(raw);
            inp.value = isNaN(num) ? '0.00' : num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function formatAll() {
            document.querySelectorAll('.month-input').forEach(function (inp) {
                if (document.activeElement !== inp) formatInput(inp);
            });
        }

        document.addEventListener('input', function (e) {
            if (!e.target.classList.contains('month-input')) return;
            var tr = e.target.closest('tr[data-row-id]');
            if (tr) { recalcRow(tr); recalcFooter(); }
        });

        document.addEventListener('focus', function (e) {
            if (!e.target.classList.contains('month-input')) return;
            e.target.value = String(e.target.value).replace(/,/g, '');
        }, true);

        document.addEventListener('blur', function (e) {
            if (!e.target.classList.contains('month-input')) return;
            formatInput(e.target);
            var tr = e.target.closest('tr[data-row-id]');
            if (tr) { recalcRow(tr); recalcFooter(); }
        }, true);

        document.addEventListener('DOMContentLoaded', function () { formatAll(); recalcAll(); });
        document.addEventListener('livewire:updated',  function () { formatAll(); recalcAll(); });

        window.toggleAll = function (master) {
            document.querySelectorAll('.row-check:not(#chk-all)').forEach(function (cb) {
                if (cb.checked !== master.checked) cb.click();
            });
        };
    })();
    </script>

</x-filament-panels::page>
