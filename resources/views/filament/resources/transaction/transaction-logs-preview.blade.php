@php
    $logs = $logs ?? [];
@endphp

<div class="space-y-2">
    @if (empty($logs))
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Fill the transaction fields to preview the generated transaction logs.
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead
                    class="bg-gray-50 dark:bg-white/5
                           text-gray-700 dark:text-gray-300
                           border-b border-gray-200 dark:border-gray-700"
                >
                    <tr class="text-left">
                        <th class="px-3 py-2">#</th>
                        <th class="px-3 py-2">Index</th>
                        <th class="px-3 py-2">Proportion</th>
                        <th class="px-3 py-2">Fx</th>
                        <th class="px-3 py-2">Concept</th>
                        <th class="px-3 py-2">Commission %</th>
                        <th class="px-3 py-2">Source</th>
                        <th class="px-3 py-2">Destination</th>
                        <th class="px-3 py-2">Gross amount</th>
                        <th class="px-3 py-2">Discount</th>
                        <th class="px-3 py-2">Banking fee</th>
                        <th class="px-3 py-2">Net amount</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ($logs as $i => $row)
                        <tr
                            class="text-gray-900 dark:text-gray-100
                                   hover:bg-gray-50 dark:hover:bg-white/5"
                        >
                            <td class="px-3 py-2">{{ $i + 1 }}</td>
                            <td class="px-3 py-2">{{ $row['index'] ?? '—' }}</td>

                            <td class="px-3 py-2">
                                {{ isset($row['proportion'])
                                    ? number_format(((float) $row['proportion']) * 100, 2) . '%'
                                    : '—'
                                }}
                            </td>

                            <td class="px-3 py-2">{{ $row['exchange_rate'] ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $row['concept'] ?? '—' }}</td>

                            <td class="px-3 py-2">
                                {{ isset($row['commission_percentage'])
                                    ? number_format(((float) $row['commission_percentage']) * 100, 6) . '%'
                                    : '—'
                                }}
                            </td>

                            <td class="px-3 py-2">{{ $row['source'] ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $row['destination'] ?? '—' }}</td>

                            <td class="px-3 py-2">
                                {{ isset($row['gross_amount']) ? number_format((float) $row['gross_amount'], 2) : '—' }}
                            </td>

                            <td class="px-3 py-2">
                                {{ isset($row['discount']) ? number_format((float) $row['discount'], 2) : '—' }}
                            </td>

                            <td class="px-3 py-2">
                                {{ isset($row['banking_fee']) ? number_format((float) $row['banking_fee'], 2) : '—' }}
                            </td>

                            <td class="px-3 py-2 font-medium">
                                {{ isset($row['net_amount']) ? number_format((float) $row['net_amount'], 2) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
