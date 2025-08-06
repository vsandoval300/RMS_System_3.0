<div class="overflow-y-auto bg-gray-950 border border-gray-800 dark:border-white/10 rounded-xl p-4 space-y-4" style="max-height: 550px;">



    
    

    {{-- DOCUMENT DETAILS --}}
   
    <h4 class="font-semibold mt-6 mb-4" style="color: #41a2c3; font-size: 15px;">
       Document Details
    </h4>



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

    <br>

    {{-- PLACEMENT SCHEMES --}}
     <h4 class="font-semibold mt-6 mb-4" style="color: #41a2c3; font-size: 15px;">
       Placement Schemes
    </h4>

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
                 <tr class="border-t border-gray-700 bg-gray-800 text-gray-300 font-semibold">
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
     <h4 class="font-semibold mt-6 mb-4" style="color: #41a2c3; font-size: 15px;">
       Insureds
    </h4>

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
                    <td class="px-2 py-1">
                        {{ collect($insureds ?? [])->unique(fn($i) => $i['company']['name'])->count() }}
                    </td> {{-- Columna vacía para # --}}
                    <td class="px-2 py-1">
                        
                        {{ collect($insureds ?? [])->unique(fn($i) => $i['company']['name'])->count() === 1 ? 'insured' : 'insureds' }}


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


    <br>



    {{-- COSTS BREAKDOWN --}}
    @php
    // Agrupamos los nodos por share
    $groupedByShare = collect($costNodes ?? [])->groupBy(fn ($node) => $node->costSchemes->share ?? 0);
    $groupIndex = 1;
    @endphp

    {{-- COSTS BREAKDOWN --}}
     <h4 class="font-semibold mt-6 mb-4" style="color: #41a2c3; font-size: 15px;">
       Costs Breakdown
    </h4>


    
    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <thead>
            
            <tr>
                <th class="px-2 py-1 text-left text-gray-400"></th>
                <th class="px-2 py-1 text-left text-gray-400"></th>
                <th class="px-2 py-1 text-left text-gray-400"></th>
                <th class="px-2 py-1 text-left text-gray-400"></th>
                <th class="px-2 py-1 text-right text-gray-400"></th>
                <th class="px-2 py-1 text-right align-middle font-semibold text-gray-300">Orig. Curr.</th>
                <th class="px-2 py-1 text-right align-middle font-semibold text-gray-300">US Dollars</th>
            </tr>

        </thead>
        <tbody>

            <tr class="bg-gray-900 text-gray-300 font-semibold">
                <td colspan="5" class="px-2 py-1 text-right">Gross Underwritten Premium</td>
                <td class="px-2 py-1 text-right border-t border-gray-600">${{ number_format($totalPremiumFts ?? 0, 2) }}</td>
                <td class="px-2 py-1 text-right border-t border-gray-600">${{ number_format($totalConvertedPremium ?? 0, 2) }}</td>
            </tr>

            
            <tr><td colspan="7" class="py-2"></td></tr>

            @forelse ($groupedCostNodes ?? [] as $group)

                <tr>
                    <td colspan="7">
                        <div class="border-t border-gray-600 my-2"></div>
                    </td>
                </tr>

                {{-- Table headers for each group --}}
                <tr class="text-sm text-gray-300 uppercase">
                    <th class="px-2 py-1 text-left">#</th>
                    <th class="px-2 py-1 text-left">Partner</th>
                    <th class="px-2 py-1 text-left">Share</th>
                    <th class="px-2 py-1 text-left">Concept</th>
                    <th class="px-2 py-1 text-right">Value</th>
                    <th class="px-2 py-1 text-right"></th>
                    <th class="px-2 py-1 text-right"></th>
                </tr>

                {{-- Deduction detail rows --}}
                @foreach ($group['nodes'] as $node)
                    <tr class="bg-gray-800 text-gray-300 border-b border-gray-600">
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
                        {{--Share {{ number_format($group['share'] * 100, 2) }}%. --}}
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
            <tr><td colspan="7" class="py-2"></td></tr>
           <tr class="bg-gray-900 text-gray-300 font-semibold">
                <td colspan="5" class="px-2 py-1 text-right">Net Underwritten Premium</td>
                <td class="px-2 py-1 text-right border-t border-gray-600">${{ number_format($totalPremiumFts - $grandTotalOrig?? 0, 2) }}</td>
                <td class="px-2 py-1 text-right border-t border-gray-600">${{ number_format($totalConvertedPremium - $grandTotalUsd ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>



    {{-- INSTALLMENTS --}}
     <h4 class="font-semibold mt-6 mb-4" style="color: #41a2c3; font-size: 15px;">
       Installments
    </h4>


    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <thead>
            <tr class="border-b border-gray-600 text-gray-400">
                <th class="px-2 py-1 text-left">#</th>
                <th class="px-2 py-1 text-right">Proportion</th>
                <th class="px-2 py-1 text-right">Exchange Rate</th>
                <th class="px-2 py-1 text-center">Due Date</th>
                <th class="px-2 py-1 text-right">Orig. Curr.</th>
                <th class="px-2 py-1 text-right">US Dollars</th>
            </tr>
        </thead>
        <tbody>
            @php
                $netPremium = ($totalPremiumFts ?? 0) - ($totalDeductionOrig ?? 0);
                $grandOrig = 0;
                $grandUsd = 0;
            @endphp

            @forelse ($transactions ?? [] as $index => $txn)
                @php
                    $proportion = floatval($txn['proportion'] ?? 0) / 100;
                    $rate = floatval($txn['exch_rate'] ?? 0);
                    $dueDate = $txn['due_date'] ?? null;

                    $amountOrig = $netPremium * $proportion;
                    $amountUsd = $amountOrig * $rate;

                    $grandOrig += $amountOrig;
                    $grandUsd += $amountUsd;
                @endphp

                <tr class="bg-gray-800 text-gray-300 border-b border-gray-700">
                   <td class="px-2 py-1">{{ $loop->iteration }}</td>
                    <td class="px-2 py-1 text-right">{{ number_format($proportion * 100, 2) }}%</td>
                    <td class="px-2 py-1 text-right">{{ number_format($rate, 4) }}</td>
                    <td class="px-2 py-1 text-center">
                        {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-2 py-1 text-right">${{ number_format($amountOrig, 2) }}</td>
                    <td class="px-2 py-1 text-right">${{ number_format($amountUsd, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-2 py-2 text-center text-gray-400">No installments available</td>
                </tr>
            @endforelse

            {{-- Grand Total row --}}
            @if (!empty($transactions))
                <tr class="border-t border-gray-700 bg-gray-800 text-gray-300 font-semibold">
                    <td colspan="4" class="px-2 py-1 text-right">Total:</td>
                    <td class="px-2 py-1 text-right">${{ number_format($grandOrig, 2) }}</td>
                    <td class="px-2 py-1 text-right">${{ number_format($grandUsd, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>







</div>
