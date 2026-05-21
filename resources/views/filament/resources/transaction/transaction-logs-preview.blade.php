@php
    $logs = $logs ?? [];
@endphp

<div class="space-y-2">
    @if (empty($logs))
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Fill the transaction fields to preview the generated transaction logs.
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">

            <table
                class="w-full text-sm"
                style="
                    border-collapse: collapse;
                    border-spacing: 0;
                    min-width: 1450px;
                    border-bottom: 2px solid #e5e7eb;
                "
            >
                {{-- HEADER --}}
                <thead
                    class="
                        bg-gray-100
                        dark:bg-white/[0.04]
                        border-b
                        border-gray-200
                        dark:border-white/10
                    "
                >
                    <tr>
                        @foreach([
                            '#',
                            'Index',
                            'Proportion',
                            'Fx',
                            'Concept',
                            'Commission %',
                            'Source',
                            'Destination',
                            'Gross amount',
                            'Discount',
                            'Banking fee',
                            'Net amount',
                        ] as $header)
                            <th
                                class="
                                    text-center
                                    font-semibold
                                    text-gray-700
                                    dark:text-gray-200
                                "
                                style="
                                    padding: 12px 16px;
                                    border-bottom: 2px solid #e5e7eb;
                                    font-size: 13px;
                                    text-align: justify;
                                    white-space: nowrap !important;
                                "
                            >
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody>
                    @foreach ($logs as $i => $row)
                        <tr
    x-data
    @mouseover="
        $el.style.backgroundColor =
            document.documentElement.classList.contains('dark')
            ? 'rgba(255,255,255,.05)'
            : '#f9fafb'
    "
    @mouseout="
        $el.style.backgroundColor = 'transparent'
    "
    style="transition: background-color .15s ease;"
>
                            <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb;">{{ $i + 1 }}</td>

                            <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb;">
                                {{ $row['index'] ?? '—' }}
                            </td>

                            <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb;">
                                {{
                                    isset($row['proportion'])
                                        ? number_format(((float) $row['proportion']) * 100, 2) . '%'
                                        : '—'
                                }}
                            </td>

                            <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb;">
                                {{ $row['exchange_rate'] ?? '—' }}
                            </td>

                            <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb;">
                                {{ $row['concept'] ?? '—' }}
                            </td>

                            <td style="padding: 10px 14px; white-space: nowrap; border-bottom: 1px solid #e5e7eb;">
                                {{
                                    isset($row['commission_percentage'])
                                        ? number_format(((float) $row['commission_percentage']) * 100, 6) . '%'
                                        : '—'
                                }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    min-width: 240px;
                                    border-bottom: 1px solid #e5e7eb;
                                "
                            >
                                {{ $row['source'] ?? '—' }}
                            </td>

                            <td
                                style="
                                    padding: 1px 14px;
                                    min-width: 240px;
                                    border-bottom: 1px solid #e5e7eb;
                                "
                            >
                                {{ $row['destination'] ?? '—' }}
                            </td>

                            <td style="padding: 10px 14px; text-align:left; white-space: nowrap; border-bottom: 1px solid #e5e7eb;">
                                {{
                                    isset($row['gross_amount'])
                                        ? number_format((float) $row['gross_amount'], 2)
                                        : '—'
                                }}
                            </td>

                            <td style="padding: 10px 14px; text-align:left; border-bottom: 1px solid #e5e7eb;">
                                {{
                                    isset($row['discount'])
                                        ? number_format((float) $row['discount'], 2)
                                        : '—'
                                }}
                            </td>

                            <td style="padding: 10px 14px; text-align:left; white-space: nowrap; border-bottom: 1px solid #e5e7eb;">
                                {{
                                    isset($row['banking_fee'])
                                        ? number_format((float) $row['banking_fee'], 2)
                                        : '—'
                                }}
                            </td>

                            <td style="padding: 10px 14px; text-align:left; font-weight:600; white-space: nowrap; border-bottom: 1px solid #e5e7eb;">
                                {{
                                    isset($row['net_amount'])
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