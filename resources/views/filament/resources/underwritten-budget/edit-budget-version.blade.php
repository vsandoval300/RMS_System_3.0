<x-filament-panels::page>

    @php
        $months = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];
        $monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $yr = $year;
    @endphp

    <style>
        /* ── Semantic tokens ── */
        .batch-card {
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
            --bc-primary:        light-dark(var(--color-primary-600), var(--color-primary-500));
            --bc-primary-subtle: light-dark(var(--color-primary-50),  var(--color-primary-950));
            --bc-primary-text:   light-dark(var(--color-primary-700), var(--color-primary-300));
            --bc-primary-border: light-dark(var(--color-primary-200), var(--color-primary-800));
            --bc-success:        light-dark(var(--color-success-600), var(--color-success-500));
            --bc-danger:         light-dark(var(--color-danger-600),  var(--color-danger-400));
        }

        .batch-card {
            background: var(--bc-card);
            border: 1px solid var(--bc-border);
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .batch-toolbar {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--bc-border);
            display: flex; flex-wrap: wrap; align-items: flex-end; gap: 1rem;
        }
        .batch-toolbar-label {
            font-size: 0.875rem; font-weight: 600;
            color: var(--bc-text-muted); margin-bottom: 0.3rem;
        }
        .batch-ctrl input[type="text"] {
            background: var(--bc-input-bg);
            border: 1px solid var(--bc-border-input);
            border-radius: 0.5rem; color: var(--bc-text);
            font-size: 0.875rem; padding: 0.45rem 0.75rem; outline: none; transition: border-color 0.15s;
        }
        .batch-ctrl input[type="text"]:focus {
            border-color: var(--bc-primary);
        }
        .readonly-pill {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: var(--bc-input-bg);
            border: 1px solid var(--bc-border-input);
            border-radius: 0.5rem; color: var(--bc-text-sec);
            font-size: 0.875rem; font-weight: 500; padding: 0.45rem 0.85rem;
        }
        .version-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            background: #41A2C3; color: #fff;
            border-radius: 0.45rem; font-size: 0.875rem; font-weight: 600;
            padding: 0.45rem 0.85rem; border: none;
        }
        .batch-notes textarea {
            background: var(--bc-input-bg);
            border: 1px solid var(--bc-border-input);
            border-radius: 0.5rem; color: var(--bc-text);
            font-size: 0.875rem; padding: 0.45rem 0.75rem;
            resize: vertical; width: 100%; outline: none; transition: border-color 0.15s;
        }
        .batch-notes textarea:focus { border-color: var(--bc-primary); }

        .batch-table-wrap { overflow-x: auto; }
        .batch-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }

        .batch-table thead tr { background: light-dark(var(--color-gray-50), #1D1D20); }
        .batch-table th {
            padding: 0.55rem 0.5rem; text-align: center;
            font-size: 0.82rem; font-weight: 600;
            color: light-dark(var(--color-gray-700), var(--color-gray-200));
            border-bottom: 1px solid var(--bc-border); white-space: nowrap;
        }
        .batch-table th.col-check  { width: 2.5rem; }
        .batch-table th.col-id     { width: 3rem; text-align: right; }
        .batch-table th.col-name   { text-align: left; min-width: 10rem; padding-left: 0.75rem; }
        .batch-table th.col-saved  { text-align: right; min-width: 6.5rem; }
        .batch-table th.col-month  { width: 8rem; }
        .batch-table th.col-total  { text-align: right; min-width: 6.5rem;
                                     background: var(--bc-primary-subtle); color: var(--bc-primary-text); }
        .batch-table th.col-delta  { text-align: right; min-width: 5rem; }

        .batch-table tbody tr { border-bottom: 1px solid var(--bc-border); transition: background 0.1s; }
        .batch-table tbody tr:hover { background: var(--bc-row-hover); }
        .batch-table tbody tr.row-excluded { opacity: 0.45; }
        .batch-table td { padding: 0.45rem 0.5rem; color: var(--bc-text-sec); vertical-align: middle; }
        .batch-table td.col-check  { text-align: center; }
        .batch-table td.col-id     { text-align: right; color: var(--bc-text-muted); font-size: 0.75rem; font-variant-numeric: tabular-nums; }
        .batch-table td.col-name   { font-weight: 500; color: var(--bc-text); padding-left: 0.75rem; }
        .batch-table td.col-saved  { text-align: right; color: var(--bc-text-muted); font-variant-numeric: tabular-nums; }
        .batch-table td.col-month  { text-align: center; }
        .batch-table td.col-total  { text-align: right; font-variant-numeric: tabular-nums; font-weight: 600;
                                     background: var(--bc-primary-subtle); color: var(--bc-primary-text); }
        .batch-table td.col-delta  { text-align: right; }

        .month-input {
            background: var(--bc-input-bg);
            border: 1px solid var(--bc-border);
            border-radius: 0.35rem; color: var(--bc-text);
            font-size: 0.8rem; font-variant-numeric: tabular-nums;
            padding: 0.3rem 0.4rem; text-align: right; width: 7.25rem; outline: none; transition: border-color 0.15s;
        }
        .month-input:focus { border-color: var(--bc-primary); }
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
        .row-check:checked { background: #41A2C3; border-color: #41A2C3; }
        .row-check:checked::after {
            content: '';
            position: absolute;
            left: 50%; top: 42%;
            transform: translate(-50%, -50%) rotate(45deg);
            width: 0.28rem; height: 0.52rem;
            border: 2px solid #fff;
            border-top: none; border-left: none;
        }

        .delta-up   { color: var(--bc-success); font-weight: 600; font-size: 0.75rem; }
        .delta-down { color: var(--bc-danger);  font-weight: 600; font-size: 0.75rem; }
        .delta-flat { color: var(--bc-text-muted); font-size: 0.75rem; }

        .notes-section {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--bc-border);
        }
        .batch-footer {
            padding: 1rem 1.25rem; border-top: 1px solid var(--bc-border);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 0.75rem;
        }
        .batch-footer-info { font-size: 0.80rem; color: var(--bc-text-muted); }
        .batch-footer-info span { font-weight: 700; color: var(--bc-text-sec); }
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
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>

    <div class="batch-card">

        {{-- Toolbar ─────────────────────────────────────────── --}}
        <div class="batch-toolbar">
            <div>
                <div class="batch-toolbar-label">Year</div>
                <div class="readonly-pill">{{ $year }}</div>
            </div>
            <div>
                <div class="batch-toolbar-label">Version</div>
                <div class="version-badge">v{{ $version }}</div>
            </div>
            <div class="batch-ctrl" style="flex:1; min-width:14rem;">
                <div class="batch-toolbar-label">Version Label <span style="color:var(--bc-danger)">*</span></div>
                <input type="text" wire:model="versionLabel"
                       placeholder="e.g. Initial Budget, Q2 Revision…" style="width:100%">
                @error('versionLabel')
                    <div style="color:#ef4444; font-size:0.75rem; margin-top:0.2rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>

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
                        <th class="col-saved">Saved Total</th>
                        @foreach($monthLabels as $i => $label)
                            <th class="col-month">
                                <span style="font-size:0.72rem; display:block; color:light-dark(#9ca3af,#6b7280);">
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
                            $savedVal = $row['py_budget'];
                            $hasSaved = $savedVal !== null;
                            $newTotal = array_sum(array_map(
                                fn($m) => (float) str_replace(',', '', $row[$m] ?? '0'),
                                $months
                            ));
                            if ($hasSaved && $savedVal > 0) {
                                $deltaPct   = (($newTotal - $savedVal) / $savedVal) * 100;
                                $deltaClass = $deltaPct > 0 ? 'delta-up' : ($deltaPct < 0 ? 'delta-down' : 'delta-flat');
                                $deltaLabel = ($deltaPct >= 0 ? '+' : '') . number_format($deltaPct, 1) . '%';
                            } else {
                                $deltaClass = 'delta-flat';
                                $deltaLabel = $hasSaved ? '—' : 'New';
                            }
                        @endphp
                        <tr data-row-id="{{ $id }}"
                            data-py="{{ $savedVal ?? 0 }}"
                            class="{{ ! $row['included'] ? 'row-excluded' : '' }}">
                            <td class="col-check">
                                <input type="checkbox"
                                       class="row-check"
                                       wire:model.live="rows.{{ $id }}.included">
                            </td>
                            <td class="col-id">{{ $id }}</td>
                            <td class="col-name">{{ $row['name'] }}</td>
                            <td class="col-saved">
                                @if($hasSaved)
                                    ${{ number_format($savedVal, 2) }}
                                @else
                                    <span style="color:light-dark(#d1d5db,#4b5563)">—</span>
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
                                style="text-align:center; padding:2rem; color:light-dark(#9ca3af,#6b7280)">
                                No reinsurers found.
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
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <script>
    (function () {
        function parseVal(s) { return parseFloat(String(s).replace(/,/g, '')) || 0; }
        function money(n) { return '$' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

        function recalcRow(tr) {
            var total = 0;
            tr.querySelectorAll('.month-input').forEach(function (inp) { total += parseVal(inp.value); });

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
