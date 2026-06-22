@php
    $logs = $logs ?? collect();
@endphp

<style>
    .tx-lifecycle-table {
        --tx-border-color: #d1d5db;
    }

    .dark .tx-lifecycle-table {
        --tx-border-color: rgba(255,255,255,.12);
    }
</style>

<div
    class="tx-lifecycle-table w-full max-w-full overflow-x-auto"
>
    <table
        class="text-sm"
        style="
            border-collapse: collapse;
            min-width: 1250px;
            width: 100%;
        "
    >
        <thead
            style="
                border-bottom: 1px solid var(--tx-border-color);
            "
        >
            <tr>
                <th style="padding: 10px 12px; text-align:left;">#</th>
                <th style="padding: 10px 12px; text-align:left;">Deduction</th>
                <th style="padding: 10px 12px; text-align:left;">Source</th>
                <th style="padding: 10px 12px; text-align:left;">Destination</th>
                <th style="padding: 10px 12px; text-align:right;">Fx</th>
                <th style="padding: 10px 12px; text-align:right;">Ded</th>
                <th style="padding: 10px 12px; text-align:right;">Gross</th>
                <th style="padding: 10px 12px; text-align:right;">Discount</th>
                <th style="padding: 10px 12px; text-align:right;">Fee</th>
                <th style="padding: 10px 12px; text-align:right;">Net</th>
                <th style="padding: 10px 12px; text-align:center;">Sent</th>
                <th style="padding: 10px 12px; text-align:center;">Received</th>
                <th style="padding: 10px 12px; text-align:center;"></th>
            </tr>
        </thead>

        <tbody>
            @forelse($logs as $log)
                @php
                    $status = strtolower(trim((string) ($log->status ?? 'Pending')));

                    $statusColor = match ($status) {
                        'completed' => '#22c55e',
                        'in process' => '#f59e0b',
                        default => '#9ca3af',
                    };
                @endphp

                <tr
                    style="
                        border-bottom: 1px solid var(--tx-border-color);
                    "
                >
                    <td style="padding: 12px;">{{ $log->index }}</td>

                    <td style="padding: 12px;">
                        {{ $log->deduction?->concept ?? '—' }}
                    </td>

                    <td style="padding: 12px; white-space: nowrap;">
                        {{ $log->fromPartner?->short_name ?? '—' }}
                    </td>

                    <td style="padding: 12px; white-space: nowrap;">
                        {{ $log->toPartner?->short_name ?? '—' }}
                    </td>

                    <td style="padding: 12px; text-align:right;">
                        {{ number_format((float) $log->exch_rate, 5) }}
                    </td>

                    <td style="padding: 12px; text-align:right;">
                        {{ number_format(((float) $log->commission_percentage) * 100, 3) }}%
                    </td>

                    <td style="padding: 12px; text-align:right; white-space: nowrap;">
                        {{ number_format((float) $log->gross_amount, 2) }}
                    </td>

                    <td style="padding: 12px; text-align:right; white-space: nowrap;">
                        {{ number_format((float) $log->commission_discount, 2) }}
                    </td>

                    <td style="padding: 12px; text-align:right;">
                        {{ number_format((float) $log->banking_fee, 2) }}
                    </td>

                    <td
                        style="
                            padding: 12px;
                            text-align:right;
                            font-weight:600;
                            white-space: nowrap;
                        "
                    >
                        {{ number_format((float) $log->net_amount, 2) }}
                    </td>

                    <td
                        style="
                            padding: 12px;
                            text-align:center;
                            white-space: nowrap;
                        "
                    >
                        {{ $log->sent_date?->format('Y-m-d') ?? '—' }}
                    </td>

                    <td
                        style="
                            padding: 12px;
                            text-align:center;
                            white-space: nowrap;
                        "
                    >
                        {{ $log->received_date?->format('Y-m-d') ?? '—' }}
                    </td>

                    <td
                        style="
                            padding: 12px;
                            text-align:center;
                        "
                    >
                        <span
                            title="{{ $log->status ?? 'Pending' }}"
                            style="
                                display:inline-block;
                                width:11px;
                                height:11px;
                                border-radius:9999px;
                                background-color:{{ $statusColor }};
                                box-shadow:0 0 0 2px rgba(156,163,175,.35);
                            "
                        ></span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        colspan="13"
                        style="
                            padding:24px;
                            text-align:center;
                            color:#9ca3af;
                        "
                    >
                        No transaction logs found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>