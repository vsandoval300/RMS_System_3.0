<x-filament-panels::page>

    @php
        $months = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];
        $monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $yr = $this->year;
    @endphp

    <style>
        .batch-card {
            background: light-dark(#ffffff, #1e2535);
            border: 1px solid light-dark(#e5e7eb, #2d3748);
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .batch-toolbar {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid light-dark(#e5e7eb, #2d3748);
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 1rem;
        }
        .batch-toolbar-label {
            font-size: 0.70rem;
            font-weight: 600;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: light-dark(#6b7280, #9ca3af);
            margin-bottom: 0.3rem;
        }
        .batch-ctrl select,
        .batch-ctrl input[type="text"] {
            background: light-dark(#f9fafb, #161d2e);
            border: 1px solid light-dark(#d1d5db, #374151);
            border-radius: 0.5rem;
            color: light-dark(#111827, #f3f4f6);
            font-size: 0.875rem;
            padding: 0.45rem 0.75rem;
            outline: none;
            transition: border-color 0.15s;
        }
        .batch-ctrl select:focus,
        .batch-ctrl input[type="text"]:focus {
            border-color: light-dark(#6366f1, #818cf8);
            box-shadow: 0 0 0 3px light-dark(rgba(99,102,241,.15), rgba(129,140,248,.15));
        }
        .batch-ctrl-year select { width: 7rem; }
        .batch-ctrl-label input { width: 22rem; }

        .batch-notes textarea {
            background: light-dark(#f9fafb, #161d2e);
            border: 1px solid light-dark(#d1d5db, #374151);
            border-radius: 0.5rem;
            color: light-dark(#111827, #f3f4f6);
            font-size: 0.875rem;
            padding: 0.45rem 0.75rem;
            resize: vertical;
            width: 100%;
            outline: none;
            transition: border-color 0.15s;
        }
        .batch-notes textarea:focus {
            border-color: light-dark(#6366f1, #818cf8);
            box-shadow: 0 0 0 3px light-dark(rgba(99,102,241,.15), rgba(129,140,248,.15));
        }

        .batch-version-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: light-dark(#ede9fe, #312e81);
            color: light-dark(#4f46e5, #a5b4fc);
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.2rem 0.7rem;
            border: 1px solid light-dark(#c4b5fd, #4338ca);
        }

        .batch-table-wrap { overflow-x: auto; }
        .batch-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }
        .batch-table thead tr {
            background: light-dark(#f3f4f6, #1a2236);
        }
        .batch-table th {
            padding: 0.55rem 0.5rem;
            text-align: center;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: light-dark(#6b7280, #9ca3af);
            border-bottom: 1px solid light-dark(#e5e7eb, #2d3748);
            white-space: nowrap;
        }
        .batch-table th.col-check   { width: 2.5rem; }
        .batch-table th.col-id      { width: 3rem; text-align: right; }
        .batch-table th.col-name    { text-align: left; min-width: 10rem; padding-left: 0.75rem; }
        .batch-table th.col-py      { text-align: right; min-width: 6.5rem; }
        .batch-table th.col-month   { width: 8rem; }
        .batch-table th.col-total   { text-align: right; min-width: 6.5rem; background: light-dark(#eef2ff, #1e2d55); }
        .batch-table th.col-delta   { text-align: right; min-width: 5rem; }

        .batch-table tbody tr {
            border-bottom: 1px solid light-dark(#f3f4f6, #1f2b3e);
            transition: background 0.1s;
        }
        .batch-table tbody tr:hover { background: light-dark(#f9fafb, #1a2236); }
        .batch-table tbody tr.row-excluded { opacity: 0.45; }

        .batch-table td {
            padding: 0.45rem 0.5rem;
            color: light-dark(#374151, #d1d5db);
            vertical-align: middle;
        }
        .batch-table td.col-check  { text-align: center; }
        .batch-table td.col-id     { text-align: right; color: light-dark(#9ca3af, #6b7280); font-size: 0.75rem; font-variant-numeric: tabular-nums; }
        .batch-table td.col-name   { font-weight: 500; color: light-dark(#111827, #f3f4f6); padding-left: 0.75rem; }
        .batch-table td.col-py     { text-align: right; color: light-dark(#6b7280, #9ca3af); font-variant-numeric: tabular-nums; }
        .batch-table td.col-month  { text-align: center; }
        .batch-table td.col-total  { text-align: right; font-variant-numeric: tabular-nums; font-weight: 600; background: light-dark(rgba(238,242,255,0.5), rgba(30,45,85,0.4)); }
        .batch-table td.col-delta  { text-align: right; }

        .month-input {
            background: light-dark(#f9fafb, #161d2e);
            border: 1px solid light-dark(#d1d5db, #374151);
            border-radius: 0.35rem;
            color: light-dark(#111827, #f3f4f6);
            font-size: 0.8rem;
            font-variant-numeric: tabular-nums;
            padding: 0.3rem 0.4rem;
            text-align: right;
            width: 7.25rem;
            outline: none;
            transition: border-color 0.15s;
        }
        .month-input:focus {
            border-color: light-dark(#6366f1, #818cf8);
            box-shadow: 0 0 0 2px light-dark(rgba(99,102,241,.12), rgba(129,140,248,.12));
        }
        .month-input:disabled { opacity: 0.35; cursor: not-allowed; }

        .row-check {
            width: 1.05rem; height: 1.05rem;
            cursor: pointer; accent-color: #6366f1;
        }

        .delta-up   { color: #22c55e; font-weight: 600; font-size: 0.75rem; }
        .delta-down { color: #ef4444; font-weight: 600; font-size: 0.75rem; }
        .delta-flat { color: light-dark(#9ca3af, #6b7280); font-size: 0.75rem; }

        .batch-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid light-dark(#e5e7eb, #2d3748);
            display: flex; align-items: center;
            justify-content: space-between;
            flex-wrap: wrap; gap: 0.75rem;
        }
        .batch-footer-info {
            font-size: 0.80rem;
            color: light-dark(#6b7280, #9ca3af);
        }
        .batch-footer-info span { font-weight: 700; color: light-dark(#374151, #d1d5db); }
        .btn-save {
            display: inline-flex; align-items: center; gap: 0.45rem;
            background: #6366f1; color: #fff; border: none;
            border-radius: 0.55rem; font-size: 0.875rem; font-weight: 600;
            padding: 0.55rem 1.25rem; cursor: pointer;
            transition: background 0.15s, transform 0.1s;
        }
        .btn-save:hover  { background: #4f46e5; }
        .btn-save:active { transform: scale(0.98); }
        .btn-cancel {
            display: inline-flex; align-items: center; gap: 0.45rem;
            background: transparent; color: light-dark(#6b7280, #9ca3af);
            border: 1px solid light-dark(#d1d5db, #374151);
            border-radius: 0.55rem; font-size: 0.875rem; font-weight: 500;
            padding: 0.55rem 1.1rem; cursor: pointer; text-decoration: none;
            transition: background 0.15s;
        }
        .btn-cancel:hover { background: light-dark(#f3f4f6, #1a2236); }
        .notes-section {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid light-dark(#e5e7eb, #2d3748);
        }
    </style>

    <div class="batch-card">

        {{-- Toolbar ─────────────────────────────────────────── --}}
        <div class="batch-toolbar">
            <div class="batch-ctrl batch-ctrl-year">
                <div class="batch-toolbar-label">Year</div>
                <select wire:model.live="year">
                    @foreach(range(now()->year - 2, now()->year + 2) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="batch-ctrl batch-ctrl-label" style="flex:1; min-width:14rem;">
                <div class="batch-toolbar-label">Version Label <span style="color:#ef4444">*</span></div>
                <input type="text"
                       wire:model="versionLabel"
                       placeholder="e.g. Initial Budget, Q2 Revision…"
                       style="width:100%">
                @error('versionLabel')
                    <div style="color:#ef4444; font-size:0.75rem; margin-top:0.2rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="padding-bottom:0.1rem">
                <div class="batch-toolbar-label">Will Save As</div>
                <div class="batch-version-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                    v{{ $this->nextVersion() }}
                </div>
            </div>
        </div>

        {{-- Notes ──────────────────────────────────────────── --}}
        <div class="notes-section">
            <div class="batch-toolbar-label" style="margin-bottom:0.35rem;">Internal Notes (optional)</div>
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
                                <span style="font-size:0.60rem; display:block; color:light-dark(#9ca3af,#6b7280);">
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

        // Real-time total on input
        document.addEventListener('input', function (e) {
            if (!e.target.classList.contains('month-input')) return;
            var tr = e.target.closest('tr[data-row-id]');
            if (tr) { recalcRow(tr); recalcFooter(); }
        });

        // Show raw on focus, format on blur
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
