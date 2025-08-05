<div class="prose dark:prose-invert max-w-none mb-24">

    {{-- DOCUMENT DETAILS --}}
    <h4 class="text-sm font-semibold mb-4">Document Details</h4>
    <table class="w-full text-sm border-separate border-spacing-y-1">
        <tbody>
            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-right font-medium text-gray-300 w-1/4">Id document:</td>
                <td class="px-2 py-1 w-1/4">{{ $id ?? '-' }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-300 w-1/4">Creation date:</td>
                <td class="px-2 py-1 w-1/4">
                    {{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d/m/Y') : '-' }}
                </td>
            </tr>
            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-right font-medium text-gray-300">Document type:</td>
                <td class="px-2 py-1">{{ $documentType ?? '-' }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-300">Period:</td>
                <td class="px-2 py-1">
                    {{ $inceptionDate ? \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') : '-' }}
                    to
                    {{ $expirationDate ? \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') : '-' }}
                </td>
            </tr>
            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-right font-medium text-gray-300">Premium type:</td>
                <td class="px-2 py-1">{{ $premiumType ?? '-' }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-300">Coverage days:</td>
                <td class="px-2 py-1">
                    {{ isset($inceptionDate, $expirationDate) ? \Carbon\Carbon::parse($inceptionDate)->diffInDays(\Carbon\Carbon::parse($expirationDate)) : '-' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- PLACEMENT SCHEMES --}}
    <h4 class="text-sm font-semibold mt-6 mb-4">Placement Schemes</h4>
    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <thead>
             <tr class="border-b border-gray-600">
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-300">#</th>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-300">Id</th>
                <th class="px-2 py-1 text-right align-middle font-medium text-gray-300">Share (%)</th>
                <th class="px-2 py-1 text-center align-middle font-medium text-gray-300">Agreement Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($costSchemes ?? [] as $index => $scheme)
                <tr class="bg-gray-800 rounded text-gray-300 border-b border-gray-600">

                    <td class="px-2 py-1">{{ $index + 1 }}</td>
                    <td class="px-2 py-1">{{ $scheme['id'] ?? '-' }}</td>
                    <td class="px-2 py-1 text-right">
                        {{ isset($scheme['share']) ? number_format($scheme['share'] * 100, 2) . '%' : '-' }}
                    </td>
                    <td class="px-2 py-1 text-center">{{ $scheme['agreement_type'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-2 py-2 text-center text-gray-400">No cost schemes available</td>
                </tr>
            @endforelse

            {{-- 🔹 TOTAL ROW --}}
            @if (isset($totalShare))
                <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                    <td colspan="2" class="px-2 py-1 text-right">Total Share:</td>
                    <td class="px-2 py-1 text-right">
                        {{ number_format($totalShare * 100, 2) . '%' }}
                    </td>
                    <td></td>
                </tr>
            @endif

        </tbody>
    </table>

    {{-- INSUREDS --}}
    <h4 class="text-sm font-semibold mt-6 mb-4">Insureds</h4>
    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <thead>
            <tr class="border-b border-gray-600">
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-300">#</th>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-300">Insured</th>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-300">Coverage</th>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-300">Country</th>
                <th class="px-2 py-1 text-right align-middle font-medium text-gray-300">Allocation</th> <!-- NUEVA -->
                <th class="px-2 py-1 text-right align-middle font-medium text-gray-300">Annual Premium</th>
                <th class="px-2 py-1 text-right align-middle font-medium text-gray-300">Annual Premium Ftp</th>
                <th class="px-2 py-1 text-right align-middle font-medium text-gray-300">Annual Premium Fts</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($insureds ?? [] as $index => $insured)
                <tr class="bg-gray-800 rounded text-gray-300 border-b border-gray-600">
                    <td class="px-2 py-1">{{ $index + 1 }}</td>
                    <td class="px-2 py-1">{{ $insured['company']['name'] ?? '-' }}</td>
                    <td class="px-2 py-1">{{ $insured['coverage']['name'] ?? '-' }}</td>
                    <td class="px-2 py-1">{{ $insured['company']['country']['name'] ?? '-' }}</td>
                    <td class="px-2 py-1 text-right">{{ number_format($insured['allocation_percent'] * 100, 2) . '%' }}</td> <!-- NUEVO -->
                    <td class="px-2 py-1 text-right">${{ number_format($insured['premium'], 2) }}</td>
                    <td class="px-2 py-1 text-right">${{ number_format($insured['premium_ftp'] ?? 0, 2) }}</td>
                    <td class="px-2 py-1 text-right">${{ number_format($insured['premium_fts'] ?? 0, 2) }}</td>

                </tr>

            @empty
                <tr>
                    <td colspan="8" class="px-2 py-2 text-center text-gray-400">No insureds available</td>
                </tr>
            @else
                {{-- 🔹 TOTAL ROW --}}
                <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                    <td class="px-2 py-1"></td> {{-- Columna vacía para # --}}
                    <td class="px-2 py-1">
                        {{ count($insureds ?? []) }} {{ count($insureds ?? []) === 1 ? 'insured' : 'insureds' }}
                    </td> {{-- 👈 Aquí ahora está el conteo, justo bajo "Insured" --}}
                    <td class="px-2 py-1"></td>
                    <td class="px-2 py-1 text-right">Totals:</td>

                    {{-- 🔹 Allocation total: debe sumar 100% --}}
                    <td class="px-2 py-1 text-right">
                        {{ number_format(collect($insureds)->sum('allocation_percent') * 100, 2) . '%' }}
                    </td>



                    <td class="px-2 py-1 text-right">
                        ${{ number_format(collect($insureds)->sum('premium'), 2) }}
                    </td>
                    <td class="px-2 py-1 text-right">
                        ${{ number_format($totalPremiumFtp ?? 0, 2) }}
                    </td>
                    <td class="px-2 py-1 text-right">
                        ${{ number_format($totalPremiumFts ?? 0, 2) }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>





    {{-- COSTS BREAKDOWN --}}
    @php
    // Agrupamos los nodos por share
    $groupedByShare = collect($costNodes ?? [])->groupBy(fn ($node) => $node->costSchemes->share ?? 0);
    $groupIndex = 1;
@endphp

{{-- COSTS BREAKDOWN --}}
<h4 class="text-sm font-semibold mt-6 mb-4">Costs Breakdown</h4>
<table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
    <thead>
        
        <tr>
            <th class="px-2 py-1 text-left text-gray-400"></th>
            <th class="px-2 py-1 text-left text-gray-400"></th>
            <th class="px-2 py-1 text-left text-gray-400"></th>
            <th class="px-2 py-1 text-left text-gray-400"></th>
            <th class="px-2 py-1 text-right text-gray-400"></th>
            <th class="px-2 py-1 text-right align-middle font-medium text-gray-300">Orig. Curr.</th>
            <th class="px-2 py-1 text-right align-middle font-medium text-gray-300">US Dollars</th>
        </tr>

        

    </thead>
    <tbody>

        <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
            <td colspan="5" class="px-2 py-1 text-right"></td>
            <td class="px-2 py-1 text-right">${{ number_format($totalPremiumFts ?? 0, 2) }}</td>
            <td class="px-2 py-1 text-right">${{ number_format($totalConvertedPremium ?? 0, 2) }}</td>
        </tr>
        
        @forelse ($groupedCostNodes ?? [] as $group)
            {{-- Deduction detail rows --}}
            @foreach ($group['nodes'] as $node)
                <tr class="bg-gray-800 text-gray-300">
                    <td class="px-2 py-1">{{ $node['index'] }}</td>
                    <td class="px-2 py-1">{{ $node['partner'] ?? '-' }}</td>
                    <td class="px-2 py-1">{{ number_format($node['share'] * 100, 2) }}%</td>
                    <td class="px-2 py-1">{{ $node['deduction'] ?? '-' }}</td>
                    <td class="px-2 py-1 text-right">{{ number_format($node['value'] * 100, 2) }}%</td>
                    <td class="px-2 py-1 text-right">${{ number_format($node['deduction_amount'], 2) }}</td>
                    <td class="px-2 py-1 text-right">${{ number_format($node['deduction_usd'], 2) }}</td>
                </tr>
            @endforeach

            {{-- Subtotal row BELOW each group --}}
            <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                <td colspan="4" class="px-2 py-1 text-left">
                    Share {{ number_format($group['share'] * 100, 2) }}%
                </td>
                <td class="px-2 py-1 text-right text-gray-300">Subtotal:</td>
                <td class="px-2 py-1 text-right">${{ number_format($group['subtotal_orig'], 2) }}</td>
                <td class="px-2 py-1 text-right">${{ number_format($group['subtotal_usd'], 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-2 py-2 text-center text-gray-400">No cost nodes available</td>
            </tr>
        @endforelse

        {{-- Grand total --}}
        @php
            $grandTotalOrig = collect($groupedCostNodes ?? [])->sum('subtotal_orig');
            $grandTotalUsd = collect($groupedCostNodes ?? [])->sum('subtotal_usd');
        @endphp
        <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
            <td colspan="5" class="px-2 py-1 text-right">Total Deductions:</td>
            <td class="px-2 py-1 text-right">${{ number_format($grandTotalOrig, 2) }}</td>
            <td class="px-2 py-1 text-right">${{ number_format($grandTotalUsd, 2) }}</td>
        </tr>
        <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
            <td colspan="5" class="px-2 py-1 text-right"></td>
            <td class="px-2 py-1 text-right">${{ number_format($totalPremiumFts - $grandTotalOrig?? 0, 2) }}</td>
            <td class="px-2 py-1 text-right">${{ number_format($totalConvertedPremium - $grandTotalUsd ?? 0, 2) }}</td>
        </tr>
    </tbody>
</table>



</div>
