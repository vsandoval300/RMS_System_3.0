@php
    use App\Models\CostScheme;

    $nodes = CostScheme::with('costNodexes.partner', 'costNodexes.deduction')
        ->find($schemeId)?->costNodexes ?? collect();

    $total = round($nodes->sum(fn ($n) => (float) ($n->value ?? 0)), 8);
    $fmtPct = fn ($v) => rtrim(rtrim(number_format($v * 100, 8, '.', ''), '0'), '.') . '%';
@endphp

@if ($nodes->isNotEmpty())
    <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm text-gray-300 border border-gray-600">
            <thead class="bg-gray-700 text-white font-semibold">
                <tr>
                    <th class="px-3 py-2 text-left  border-b border-gray-600">#</th>
                    <th class="px-3 py-2 text-left  border-b border-gray-600">Partner</th>
                    <th class="px-3 py-2 text-left  border-b border-gray-600">Concept</th>
                    <th class="px-3 py-2 text-left  border-b border-gray-600">Referral Partner</th>
                    <th class="px-3 py-2 text-right border-b border-gray-600">Value</th> {{-- ← Alineación derecha --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($nodes as $node)
                    <tr class="bg-gray-800 border-t border-gray-600">
                        <td class="px-3 py-2 font-semibold">{{ $node->index }}</td>
                        <td class="px-3 py-2">{{ $node->partner->name ?? 'N/A' }}</td>
                        <td class="px-3 py-2">{{ $node->deduction?->concept ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $node->referral_partner ?? '—' }}</td>
                        <td class="px-3 py-2 text-right text-blue-400"> {{-- ← Alineación derecha --}}
                            {{ $fmtPct($node->value ?? 0) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tr class="bg-gray-800 border-t border-gray-600">
       
            <tfoot>
                <tr>
                    <td class="px-3 py-2" colspan="3"></td>
                    <td class="px-3 py-2 text-right font-semibold">Total</td>
                    <td class="px-3 py-2 text-right font-semibold text-blue-300">
                        {{ $fmtPct($total) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="text-sm text-gray-400 italic">Este esquema no tiene nodos definidos.</div>
@endif


