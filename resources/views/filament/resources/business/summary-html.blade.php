<div class="prose dark:prose-invert max-w-none">
     {{-- DOCUMENT DETAILS --}}
    <h4 class="text-sm font-semibold mb-4">Document Details</h4>

    <table class="w-full text-sm border-separate border-spacing-y-1">
        <tbody>
            <tr>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Id document:</td>
                <td class="px-2 py-1">{{ $id }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Creation date:</td>
                <td class="px-2 py-1">{{ \Carbon\Carbon::parse($createdAt)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Document type:</td>
                <td class="px-2 py-1">{{ $documentType }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Period:</td>
                <td class="px-2 py-1">
                    {{ \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') }}
                    to
                    {{ \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') }}
                </td>
            </tr>
            <tr>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Premium type:</td>
                <td class="px-2 py-1">{{ $premiumType ?? '-' }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Coverage days:</td>
                <td class="px-2 py-1">
                    {{ \Carbon\Carbon::parse($inceptionDate)->diffInDays(\Carbon\Carbon::parse($expirationDate)) }}
                </td>
            </tr>
        </tbody>
    </table>

    <hr class="my-4" />


    {{-- PLACEMENT SCHEMES --}}
    <h4 class="text-sm font-semibold mt-4">Placement Schemes</h4>




    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <thead>
            <tr>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-400">#</th>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-400">Id</th>
                <th class="px-2 py-1 text-right align-middle font-medium text-gray-400">Share (%)</th>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-400">Agreement Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($costSchemes as $index => $scheme)
                <tr class="bg-gray-800 rounded">
                    <td class="px-2 py-1">{{ $index + 1 }}</td>
                    <td class="px-2 py-1">{{ $scheme->costScheme->id ?? '-' }}</td>
                    <td class="px-2 py-1 text-right">
                        {{ number_format(($scheme->costScheme->share ?? 0) * 100, 2) }}%
                    </td>
                    <td class="px-2 py-1">
                        {{ $scheme->costScheme->agreement_type ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-2 py-2 text-center text-gray-400">No cost schemes available</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    <hr class="my-4" />

     {{-- INSUREDS --}}
    <h4 class="text-sm font-semibold mt-4">Insureds</h4>

    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <thead>
            <tr>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-400">#</th>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-400">Insured</th>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-400">Coverage</th>
                <th class="px-2 py-1 text-left align-middle font-medium text-gray-400">Country</th>
                <th class="px-2 py-1 text-right align-middle font-medium text-gray-400">Allocation</th>
                <th class="px-2 py-1 text-center align-middle font-medium text-gray-400">Annual<br>Premium</th>
                <th class="px-2 py-1 text-center align-middle font-medium text-gray-400">Annual<br>Premium Ftp</th>
                <th class="px-2 py-1 text-center align-middle font-medium text-gray-400">Annual<br>Premium Fts</th>
            </tr>
        </thead>
       <tbody>
    @php
        $coverageDays = \Carbon\Carbon::parse($inceptionDate)->diffInDays(\Carbon\Carbon::parse($expirationDate));
        $daysInYear = \Carbon\Carbon::parse($inceptionDate)->isLeapYear() ? 366 : 365;
        $share = optional($costSchemes->first()?->costScheme)->share ?? 0;

        $totalAllocation = 0;
        $totalPremium = 0;
        $totalPremiumFtp = 0;
        $totalPremiumFts = 0;
        $totalConvertedPremium = 0; // ⭐️ NUEVA VARIABLE ACUMULADORA

        // 🟢 Calculamos $totalPremium y $totalPremiumFts una sola vez
        $totalPremium = $insureds->sum('premium');
        $totalPremiumFtp = ($totalPremium / $daysInYear) * $coverageDays;
        $totalPremiumFts = $totalPremiumFtp * $share;

        // 🟢 Calculamos $totalConvertedPremium en base a transacciones del documento
        $transactions = $record->transactions ?? collect();
        foreach ($transactions as $txn) {
            $converted = $totalPremiumFts * $txn->proportion * $txn->exch_rate;
            $totalConvertedPremium += $converted;
        }



    @endphp

    @forelse ($insureds as $index => $insured)
        @php
            $allocation = $insured->premium > 0 && $insureds->sum('premium') > 0
                ? ($insured->premium / $insureds->sum('premium')) * 100
                : 0;

            $premium = $insured->premium;
            $premiumFtp = ($premium / $daysInYear) * $coverageDays;
            $premiumFts = $premiumFtp * $share;

            // ⭐️ NUEVO CÁLCULO DEL MONTO CONVERTIDO POR TRANSACCIONES
            
            $transactions = $record->transactions;

            // Acumuladores
            $totalAllocation += $allocation;
           
        @endphp

        <tr class="bg-gray-800 rounded">
            <td class="px-2 py-1">{{ $index + 1 }}</td>
            <td class="px-2 py-1">{{ $insured->company->name ?? '-' }}</td>
            <td class="px-2 py-1">{{ $insured->coverage->name ?? '-' }}</td>
            <td class="px-2 py-1">{{ $insured->company->country->name ?? '-' }}</td>
            <td class="px-2 py-1 text-right">
                {{ number_format($allocation, 2) }}%
            </td>
            <td class="px-2 py-1 text-right">
                ${{ number_format($premium, 2) }}
            </td>
            <td class="px-2 py-1 text-right">
                ${{ number_format($premiumFtp, 2) }}
            </td>
            <td class="px-2 py-1 text-right">
                ${{ number_format($premiumFts, 2) }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="px-2 py-2 text-center text-gray-400">No insureds available</td>
        </tr>
    @endforelse

    {{-- Totales --}}
    <tr class="bg-gray-900 font-semibold text-gray-400 border-t border-gray-700">
        <td class="px-2 py-1 text-right " colspan="4">Totals:</td>
        <td class="px-2 py-1 text-right ">{{ number_format($totalAllocation, 2) }}%</td>
        <td class="px-2 py-1 text-right ">${{ number_format($totalPremium, 2) }}</td>
        <td class="px-2 py-1 text-right ">${{ number_format($totalPremiumFtp, 2) }}</td>
        <td class="px-2 py-1 text-right ">${{ number_format($totalPremiumFts, 2) }}</td>
    </tr>
</tbody>
    </table>


{{-- COSTS BREAKDOWN --}}
    <h4 class="text-sm font-semibold mt-4">Costs Breakdown</h4>

    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <thead>

            {{-- COSTS BREAKDOWN --}}

            


            {{-- Encabezados reales --}}
            <tr>
                <th class="px-2 py-1 text-left text-gray-400">#</th>
                <th class="px-2 py-1 text-left text-gray-400">Partner</th>
                <th class="px-2 py-1 text-left text-gray-400"></th> 
                <th class="px-2 py-1 text-right text-gray-400"></th>
                <th class="px-2 py-1 text-center text-gray-400">
                    Orig. Curr.<br>
                    <span class="text-gray-400 font-semibold">${{ number_format($totalPremiumFts, 2) }}</span>
                </th>
                <th class="px-2 py-1 text-center text-gray-400">
                    US Dollars<br>
                    <span class="text-gray-400 font-semibold">${{ number_format($totalConvertedPremium, 2) }}</span>
                </th>
            </tr>

        </thead>
        {{-- Valores de la Tabla --}}
        <tbody>
            @forelse ($costNodes as $node)
                <tr class="bg-gray-800 rounded">
                    <td class="px-2 py-1">{{ $node->index }}</td>
                    <td class="px-2 py-1">{{ $node->partner->name ?? '-' }}</td>
                    <td class="px-2 py-1">{{ $node->deduction->concept  ?? '-' }}</td> 
                    <td class="px-2 py-1 text-right">
                        {{ number_format($node->value * 100, 2) }}%
                    </td>
                    <td class="px-2 py-1 text-right">
                        @php
                            $deduction = $totalPremiumFts * $node->value;
                        @endphp
                        ${{ number_format($deduction, 2) }}
                    </td>
                    <td class="px-2 py-1 text-right">
                        @php
                            $deductionConverted = $totalConvertedPremium * $node->value;
                        @endphp
                        ${{ number_format($deductionConverted, 2) }}
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-2 py-2 text-center text-gray-400">No cost nodes available</td>
                </tr>
            @endforelse
        </tbody>
    </table>









