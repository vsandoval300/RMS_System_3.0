<div class="overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-left">
      <tr class="text-gray-500 uppercase tracking-wide">
        <th class="py-2 pr-4">Index</th>
        <th class="py-2 pr-4">Deduction type</th>
        <th class="py-2 pr-4">From_entity</th>
        <th class="py-2 pr-4">To_entity</th>
        <th class="py-2 pr-4">Exch_rate</th>
        <th class="py-2 pr-4">Gross_amount</th>
        <th class="py-2 pr-4">Discount</th>
        <th class="py-2 pr-4">Banking fee</th>
        <th class="py-2 pr-4">Net_amount</th>
        <th class="py-2 pr-0">Status</th>
      </tr>
    </thead>

    <tbody class="divide-y divide-white/5">
      @forelse($rows as $r)
        <tr>
          <td class="py-2 pr-4 font-medium">{{ $r['inst_index'] }}.{{ $r['index'] }}</td>
          <td class="py-2 pr-4">{{ $r['deduction'] }}</td>
          <td class="py-2 pr-4">{{ $r['from'] }}</td>
          <td class="py-2 pr-4">{{ $r['to'] }}</td>
          <td class="py-2 pr-4">{{ number_format((float)($r['exch_rate'] ?? 0), 6, '.', ',') }}</td>
          <td class="py-2 pr-4">
            {{ is_null($r['gross']) ? '-' : number_format((float)$r['gross'], 2, '.', ',') }}
          </td>
          <td class="py-2 pr-4">
            {{ is_null($r['discount']) ? '-' : number_format((float)$r['discount'], 2, '.', ',') }}
          </td>
          <td class="py-2 pr-4">
            {{ is_null($r['banking_fee']) ? '-' : number_format((float)$r['banking_fee'], 2, '.', ',') }}
          </td>
          <td class="py-2 pr-4">
            {{ is_null($r['net']) ? '-' : number_format((float)$r['net'], 2, '.', ',') }}
          </td>
          <td class="py-2 pr-0">
            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs ring-1 ring-inset
              @class([
                'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20' => ($r['status'] ?? '') === 'received',
                'bg-blue-500/10 text-blue-400 ring-blue-500/20'         => ($r['status'] ?? '') === 'sent',
                'bg-orange-500/10 text-orange-400 ring-orange-500/20'   => ($r['status'] ?? '') === 'pending',
                'bg-zinc-500/10 text-zinc-300 ring-zinc-500/20'         => !in_array(($r['status'] ?? ''), ['received','sent','pending']),
              ])
            ">
              {{ ucfirst($r['status'] ?? '-') }}
            </span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="py-4 text-center text-gray-500">No logs found for current installments.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

