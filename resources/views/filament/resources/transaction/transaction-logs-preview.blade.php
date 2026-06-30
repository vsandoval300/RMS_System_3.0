@php
    $logs = $logs ?? [];
@endphp

<style>
    .rms-preview-table {
        --rms-border-color: #e5e7eb;
    }

    .dark .rms-preview-table {
        --rms-border-color: rgba(255, 255, 255, 0.12);
    }
</style>

<div class="space-y-2">
    @if (empty($logs))
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Fill the transaction fields to preview the generated transaction logs.
        </div>
    @else
        <div
            class="rms-preview-table"
            style="
                width: 100%;
                max-width: 100%;
                overflow-x: auto;
                overflow-y: hidden;
            "
        >

            <table
                style="
                    border-collapse: collapse;
                    border-spacing: 0;
                    min-width: 1100px;
                    width: max-content;
                    margin-left: auto;
                    margin-right: auto;
                    border-bottom: 2px solid var(--rms-border-color);
                "
            >
                {{-- HEADER --}}
                <thead
                    class="
                        bg-gray-100
                        dark:bg-white/[0.04]
                    "
                >
                    <tr>
                        @foreach([
                            'Index',
                            'Proportion',
                            'Fx',
                            'Concept',
                            'Commission %',
                            'Source',
                            'Destination',
                            'Premium Fts',
                            'Discount',
                            'Banking fee',
                            'Net amount',
                        ] as $header)
                            <th
                                class="
                                    font-semibold
                                    text-gray-700
                                    dark:text-gray-200
                                "
                                style="
                                    padding: 10px 14px;
                                    border-bottom: 2px solid var(--rms-border-color);
                                    font-size: 13px;
                                    text-align: left;
                                    white-space: nowrap;
                                "
                            >
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody>
                    @foreach ($logs as $row)
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

                            <td
                                style="
                                    padding: 10px 14px;
                                    text-align: center;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
                                {{ $row['index'] ?? '—' }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
                                {{
                                    isset($row['proportion'])
                                        ? number_format(((float) $row['proportion']) * 100, 2) . '%'
                                        : '—'
                                }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
                                {{ $row['exchange_rate'] ?? '—' }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
                                {{ $row['concept'] ?? '—' }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    white-space: nowrap;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
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
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
                                {{ $row['source'] ?? '—' }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    min-width: 240px;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
                                {{ $row['destination'] ?? '—' }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    text-align: left;
                                    white-space: nowrap;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
                                {{
                                    isset($row['gross_amount'])
                                        ? number_format((float) $row['gross_amount'], 2)
                                        : '—'
                                }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    text-align: left;
                                    white-space: nowrap;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
                                {{
                                    isset($row['discount'])
                                        ? number_format((float) $row['discount'], 2)
                                        : '—'
                                }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    text-align: left;
                                    white-space: nowrap;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
                                {{
                                    isset($row['banking_fee'])
                                        ? number_format((float) $row['banking_fee'], 2)
                                        : '—'
                                }}
                            </td>

                            <td
                                style="
                                    padding: 10px 14px;
                                    text-align: left;
                                    white-space: nowrap;
                                    border-bottom: 1px solid var(--rms-border-color);
                                "
                            >
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