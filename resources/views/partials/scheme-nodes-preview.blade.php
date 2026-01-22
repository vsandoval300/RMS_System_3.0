@php
    use App\Models\CostScheme;

    $nodes = CostScheme::with('costNodexes.partner', 'costNodexes.deduction')
        ->find($schemeId)?->costNodexes ?? collect();

    $total = round($nodes->sum(fn ($n) => (float) ($n->value ?? 0)), 8);
    $fmtPct = fn ($v) => rtrim(rtrim(number_format($v * 100, 8, '.', ''), '0'), '.') . '%';
@endphp

@if ($nodes->isNotEmpty())
    <div class="overflow-x-auto">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="table-auto w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">#</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">Concept</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">Source</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">Destination</th>
                        <th class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">Value</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-900">
                    @foreach ($nodes as $node)
                        <tr class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                            <td class="px-3 py-2 font-semibold text-gray-700 dark:text-gray-200">
                                {{ $node->index }}
                            </td>

                            <td class="px-3 py-2 text-gray-700 dark:text-gray-200">
                                {{ $node->deduction?->concept ?? '—' }}
                            </td>

                            <td class="px-3 py-2 text-gray-700 dark:text-gray-200">
                                {{ $node->partnerSource->short_name ?? 'N/A' }}
                            </td>

                            <td class="px-3 py-2 text-gray-700 dark:text-gray-200">
                                {{ $node->partnerDestination->short_name ?? '—' }}
                            </td>

                            <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">
                                {{ $fmtPct($node->value ?? 0) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot class="bg-gray-50 dark:bg-gray-800">
                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="px-3 py-2" colspan="3"></td>
                        <td class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">Total</td>
                        <td class="px-3 py-2 text-right font-semibold tabular-nums text-gray-700 dark:text-gray-200">
                            {{ $fmtPct($total) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@else
    <div class="text-sm italic text-gray-500 dark:text-gray-400">
        No nodes have been defined for this scheme.
    </div>
@endif


