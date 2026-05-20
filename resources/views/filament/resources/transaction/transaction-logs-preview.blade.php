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
            <table class="w-full min-w-max table-auto border-collapse text-sm">
                
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-3 py-2 text-left whitespace-nowrap">#</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Index</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Proportion</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Fx</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Concept</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Commission %</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Source</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Destination</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Gross amount</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Discount</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Banking fee</th>
                        <th class="px-3 py-2 text-left whitespace-nowrap">Net amount</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ($logs as $i => $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-3 py-2 whitespace-nowrap">{{ $i + 1 }}</td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                {{ $row['index'] ?? '—' }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                {{ isset($row['proportion'])
                                    ? number_format(((float) $row['proportion']) * 100, 2) . '%'
                                    : '—'
                                }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                {{ $row['exchange_rate'] ?? '—' }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                {{ $row['concept'] ?? '—' }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                {{ isset($row['commission_percentage'])
                                    ? number_format(((float) $row['commission_percentage']) * 100, 6) . '%'
                                    : '—'
                                }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                {{ $row['source'] ?? '—' }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap">
                                {{ $row['destination'] ?? '—' }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap text-right">
                                {{ isset($row['gross_amount'])
                                    ? number_format((float) $row['gross_amount'], 2)
                                    : '—'
                                }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap text-right">
                                {{ isset($row['discount'])
                                    ? number_format((float) $row['discount'], 2)
                                    : '—'
                                }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap text-right">
                                {{ isset($row['banking_fee'])
                                    ? number_format((float) $row['banking_fee'], 2)
                                    : '—'
                                }}
                            </td>

                            <td class="px-3 py-2 whitespace-nowrap text-right">
                                {{ isset($row['net_amount'])
                                    ? number_format((float) $row['net_amount'], 2)
                                    : '—'
                                }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
