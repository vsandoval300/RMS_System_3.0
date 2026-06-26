@php $rows = $this->getOverdueTransactions(); @endphp

<x-filament::section heading="Overdue Transactions" icon="heroicon-o-exclamation-triangle" icon-color="danger">

    @if ($rows->isEmpty())
        <p style="text-align:center; padding: 1.5rem 0; color:#6b7280; font-size:0.875rem;">
            No overdue transactions found.
        </p>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(156,163,175,0.25);">
                        <th style="padding:8px 20px 8px 0; text-align:left; font-weight:600; color:#9ca3af; white-space:nowrap; font-size:0.85rem; letter-spacing:0.02em;">Reference</th>
                        <th style="padding:8px 20px 8px 0; text-align:left; font-weight:600; color:#9ca3af; white-space:nowrap; font-size:0.85rem; letter-spacing:0.02em;">Reinsurer</th>
                        <th style="padding:8px 20px 8px 0; text-align:left; font-weight:600; color:#9ca3af; white-space:nowrap; font-size:0.85rem; letter-spacing:0.02em;">Type</th>
                        <th style="padding:8px 20px 8px 0; text-align:left; font-weight:600; color:#9ca3af; white-space:nowrap; font-size:0.85rem; letter-spacing:0.02em;">Due Date</th>
                        <th style="padding:8px 20px 8px 0; text-align:center; font-weight:600; color:#9ca3af; white-space:nowrap; font-size:0.85rem; letter-spacing:0.02em;">Days Late</th>
                        <th style="padding:8px 20px 8px 0; text-align:right; font-weight:600; color:#9ca3af; white-space:nowrap; font-size:0.85rem; letter-spacing:0.02em;">Amount</th>
                        <th style="padding:8px 0; text-align:left; font-weight:600; color:#9ca3af; white-space:nowrap; font-size:0.85rem; letter-spacing:0.02em; min-width:110px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr style="border-bottom:1px solid rgba(156,163,175,0.1); transition:background 0.15s;"
                            onmouseover="this.style.background='rgba(156,163,175,0.05)'"
                            onmouseout="this.style.background=''">

                            <td style="padding:12px 20px 12px 0; white-space:nowrap;">
                                <a href="{{ $row->edit_url }}"
                                   style="font-family:ui-monospace,monospace; color:#818cf8; text-decoration:none; font-size:0.8rem;"
                                   onmouseover="this.style.textDecoration='underline'"
                                   onmouseout="this.style.textDecoration='none'">
                                    {{ $row->reference }}
                                </a>
                            </td>

                            <td style="padding:12px 20px 12px 0; color:#d1d5db; white-space:nowrap;">
                                {{ $row->reinsurer }}
                            </td>

                            <td style="padding:12px 20px 12px 0; color:#9ca3af; white-space:nowrap;">
                                {{ $row->type }}
                            </td>

                            <td style="padding:12px 20px 12px 0; color:#d1d5db; white-space:nowrap;">
                                {{ \Illuminate\Support\Carbon::parse($row->due_date)->format('d M Y') }}
                            </td>

                            <td style="padding:12px 20px 12px 0; text-align:center; white-space:nowrap;">
                                <span style="display:inline-block; padding:2px 10px; border-radius:9999px; font-size:0.75rem; font-weight:700; background:rgba(239,68,68,0.15); color:#f87171;">
                                    {{ $row->days_overdue }}d
                                </span>
                            </td>

                            <td style="padding:12px 20px 12px 0; text-align:right; font-family:ui-monospace,monospace; color:#d1d5db; white-space:nowrap;">
                                ${{ number_format((float) $row->net_amount, 2) }}
                            </td>

                            <td style="padding:12px 0; white-space:nowrap; min-width:110px;">
                                <x-filament::badge :color="$row->status === 'In process' ? 'warning' : 'gray'">
                                    {{ $row->status }}
                                </x-filament::badge>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</x-filament::section>
