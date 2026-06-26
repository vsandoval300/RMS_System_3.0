<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Reinsurer</th>
            <th>Document</th>
            <th>Index</th>
            <th>Proportion</th>
            <th>Exchange Rate</th>
            <th>Due Date</th>
            <th>Remittance</th>
            <th>Type</th>
            <th>Net Amount</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $i => $transaction)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $transaction->operativeDoc?->business?->reinsurer?->short_name }}</td>
                <td>{{ $transaction->op_document_id }}</td>
                <td>{{ $transaction->index }}</td>
                <td>{{ number_format(((float) $transaction->proportion) * 100, 2) }}%</td>
                <td>{{ $transaction->exch_rate }}</td>
                <td>{{ optional($transaction->due_date)->format('Y-m-d') }}</td>
                <td>{{ $transaction->remmitance_code }}</td>
                <td>{{ $transaction->type?->description }}</td>
                <td>{{ number_format((float) ($transaction->latest_net_amount ?? 0), 2) }}</td>
                <td>{{ $transaction->status?->transaction_status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>