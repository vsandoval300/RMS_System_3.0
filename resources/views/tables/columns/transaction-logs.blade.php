
<x-filament::section compact>
    @php
        /** @var \Illuminate\Support\Collection|\App\Models\TransactionLog[] $logs */
        $logs = collect($getState() ?? []);
    @endphp


    <div class="text-sm font-semibold mb-2">Transaction log</div>

    @forelse ($logs as $i => $log)
        @if ($loop->first)
            <table class="w-full text-sm border-separate border-spacing-y-1">
                <thead class="text-gray-400">
                    <tr>
                        <th class="px-2 py-1 text-left">#</th>
                        <th class="px-2 py-1 text-left">Deduction type</th>
                        <th class="px-2 py-1 text-left">From → To</th>
                        <th class="px-2 py-1 text-right">Gross</th>
                        <th class="px-2 py-1 text-right">Comm. disc.</th>
                        <th class="px-2 py-1 text-right">Bank fee</th>
                        <th class="px-2 py-1 text-right">Net</th>
                        <th class="px-2 py-1 text-left">Sent / Received</th>
                        <th class="px-2 py-1 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
        @endif

                    <tr class="align-top">
                        <td class="px-2 py-1">{{ $loop->iteration }}</td>
                        <td class="px-2 py-1">{{ $log->deduction_type }}</td>
                        <td class="px-2 py-1">{{ $log->from_entity }} → {{ $log->to_entity }}</td>
                        <td class="px-2 py-1 text-right">{{ number_format((float) $log->gross_amount, 2) }}</td>
                        <td class="px-2 py-1 text-right">{{ number_format((float) $log->commission_discount, 2) }}</td>
                        <td class="px-2 py-1 text-right">{{ number_format((float) $log->banking_fee, 2) }}</td>
                        <td class="px-2 py-1 text-right">{{ number_format((float) $log->net_amount, 2) }}</td>
                        <td class="px-2 py-1">
                            <div>Sent: {{ optional($log->sent_date)->format('M d, Y') }}</div>
                            <div>Recv: {{ optional($log->received_date)->format('M d, Y') }}</div>
                        </td>
                        <td class="px-2 py-1">
                            <span class="px-2 py-0.5 rounded-full bg-gray-700 text-gray-200">
                                {{ $log->status }}
                            </span>
                        </td>
                    </tr>

        @if ($loop->last)
                </tbody>
            </table>
        @endif
    @empty
        <div class="text-sm text-gray-400">No logs for this transaction.</div>
    @endforelse
</x-filament::section>


