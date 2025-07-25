@php
    $nodes = \App\Models\CostScheme::with('costNodexes.partner', 'costNodexes.deduction')
        ->find($schemeId)?->costNodexes ?? collect();
@endphp

@if ($nodes->isNotEmpty())
    <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm text-gray-300 border border-gray-600">
            <thead class="bg-gray-700 text-white font-semibold">
                <tr>
                    <th class="px-3 py-2 text-left border-b border-gray-600">#</th>
                    <th class="px-3 py-2 text-left border-b border-gray-600">Partner</th>
                    <th class="px-3 py-2 text-left border-b border-gray-600">Concept</th>
                    <th class="px-3 py-2 text-left border-b border-gray-600">Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nodes as $node)
                    <tr class="bg-gray-800 border-t border-gray-600">
                        <td class="px-3 py-2 font-semibold">{{ $node->index }}</td>
                        <td class="px-3 py-2">{{ $node->partner->name ?? 'N/A' }}</td>
                        <td class="px-3 py-2">{{ $node->deduction?->concept ?? '—' }}</td>
                        <td class="px-3 py-2 text-blue-400">
                            {{ rtrim(rtrim(number_format($node->value * 100, 8, '.', ''), '0'), '.') }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-sm text-gray-400 italic">Este esquema no tiene nodos definidos.</div>
@endif



