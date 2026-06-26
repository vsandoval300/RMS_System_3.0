@php
    use App\Models\CostScheme;

    $nodes = CostScheme::with('costNodexes.partner', 'costNodexes.deduction')
        ->find($schemeId)?->costNodexes ?? collect();

    $total = round($nodes->sum(fn ($n) => (float) ($n->value ?? 0)), 8);
    $fmtPct = fn ($v) => rtrim(rtrim(number_format($v * 100, 8, '.', ''), '0'), '.') . '%';
@endphp

@if ($nodes->isNotEmpty())
<div class="overflow-x-auto">
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="fi-ta-table w-full table-fixed border-separate border-spacing-0 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr class="align-middle border-b border-gray-200 dark:border-gray-700">

                    <th class="w-36 px-3 py-3 text-left font-semibold" style="text-align: left;">
                        #
                    </th>

                    <th class="w-36 px-3 py-3 text-left font-semibold" style="text-align: left;">
                        Concept
                    </th>

                    <th class="px-3 py-3 text-left font-semibold" style="text-align: left;">
                        Source
                    </th>

                    <th class="px-3 py-3 text-left font-semibold" style="text-align: left;">
                        Destination
                    </th>

                    <th class="w-28 px-3 py-3 text-right font-semibold" style="text-align: right;">
                        Value
                    </th>

                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @foreach ($nodes as $node)
                    <tr class="align-middle">
                        <td class="px-3 py-2 font-semibold w-36">
                            {{ $node->index }}
                        </td>
                        <td class="truncate px-3 py-2" style="text-align: left;">
                            {{ $node->deduction?->concept ?? '—' }}
                        </td>
                        <td class="truncate px-3 py-2" style="text-align: left;">
                            {{ $node->partnerSource->short_name ?? 'N/A' }}
                        </td>
                        <td class="truncate px-3 py-2" style="text-align: left;">
                            {{ $node->partnerDestination->short_name ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-right tabular-nums" style="text-align: right;">
                            {{ $fmtPct($node->value ?? 0) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-800">
                <tr class="border-t border-gray-200 dark:border-gray-700">

                    <td colspan="4"
                        class="px-3 py-2 text-right font-semibold" style="text-align: right;">
                        Total
                    </td>

                    <td class="px-3 py-2 text-right font-semibold tabular-nums" style="text-align: right;">
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


