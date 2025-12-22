<div class="overflow-y-auto border border-gray-800 rounded p-4 space-y-4" style="max-height: 1200px; background-color: #f1efea; color: #1f262a;font-family: 'Montserrat', sans-serif;">

    <head>
    <!-- Otras etiquetas head dfe0e2-->
        <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    </head>

     {{-- MAIN TITTLE --}}
    {{-- <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px;"> }}
       <td class="px-2 py-1 text-right font-extrabold text-gray-300 w-1/4"></td>
        <td class="px-2 py-1 w-1/4">{{ $id ?? '-' }}</td>
    </h4> --}}

    <h4 class="font-semibold mt-6 mb-4" style="color:#db4a2b;font-size:15px;">
        <span class="px-2 py-1 text-left font-extrabold text-gray-300 w-1/4"></span>
        <span class="px-2 py-1 w-1/4">{{ $id ?? '-' }}</span>
     </h4>
    
     {{--CURRENT DATE 
    <div class="text-right text-sm font-medium text-gray-600 mb-2">
        Date: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div> --}}

    {{-------------------------------------------------------------------------------}}
    {{-- DOCUMENT DETAILS SECTION                                                  --}}
    {{-------------------------------------------------------------------------------}}
    <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px;">
       General Details
    </h4>

    <table class="table-fixed w-full text-sm border-separate border-spacing-y-1">
        <colgroup>
            {{-- 4 columnas con datos → auto --}}
            <col>
            <col>
            <col>
            <col>

            {{-- 2 columnas dummy con ancho fijo --}}
            <col style="width: 150px;">
            <col style="width: 150px;">
            {{-- o Tailwind:
            <col class="w-[80px]">
            <col class="w-[80px]">
            --}}
        </colgroup>

        <tbody>
            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">
                    Document type:
                </td>
                <td class="px-2 py-1 font-thin">{{ $documentType ?? '-' }}</td>
                <td class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">
                    Creation date:
                </td>
                <td class="px-2 py-1">
                    {{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d/m/Y') : '-' }}
                </td>
                {{-- columnas “dummy” --}}
                <td></td>
                <td></td>
            </tr>

            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">
                    Premium type:
                </td>
                <td class="px-2 py-1 font-thin">{{ $premiumType ?? '-' }}</td>
                <td class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">
                    Period:
                </td>
                <td class="px-2 py-1 font-thin">
                    {{ $inceptionDate ? \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') : '-' }}
                    to
                    {{ $expirationDate ? \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') : '-' }}
                </td>
                <td></td>
                <td></td>
            </tr>

            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">
                    Original currency:
                </td>
                <td class="px-2 py-1 font-thin">{{ $originalCurrency ?? '-' }}</td>
                <td class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">
                    Coverage days:
                </td>
                <td class="px-2 py-1 font-thin">
                    {{ isset($inceptionDate, $expirationDate)
                        ? \Carbon\Carbon::parse($inceptionDate)->diffInDays(\Carbon\Carbon::parse($expirationDate))
                        : '-' }}
                </td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <br>

    {{-------------------------------------------------------------------------------}}
    {{-- PLACEMENT SCHEMES                                                         --}}
    {{-------------------------------------------------------------------------------}}
     <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px;">
       Placement Schemes
    </h4>

    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <thead>
             <tr class="border-b text-gray-300 border-gray-600">
                <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">#</th>
                <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">Id</th>
                <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">Description</th>
                <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d;">Share (%)</th>
                <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d;">Agreement Type</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($costSchemes ?? [] as $index => $scheme)
                <tr class="bg-gray-800 rounded text-gray-300 border-b border-gray-600">

                    <td class="px-2 py-1">{{ $index + 1 }}</td>
                    <td class="px-2 py-1">{{ $scheme['id'] ?? '-' }}</td>

                    <td class="px-2 py-1"> {{-- ✅ MOD [PS-DESC-3] NEW --}}
                        {{ $scheme['description'] ?? '-' }}
                    </td>

                    <td class="px-2 py-1 text-center">
                        {{ isset($scheme['share']) ? number_format($scheme['share'] * 100, 2) . '%' : '-' }}
                    </td>
                    <td class="px-2 py-1 text-center">{{ $scheme['agreement_type'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-2 py-2 text-center text-gray-400">No cost schemes available</td>
                </tr>
            @endforelse

            {{-- 🔹 TOTAL ROW 
            @if (isset($totalShare))
                 <tr class="border-t border-gray-700 bg-gray-800 text-gray-300 font-semibold">
                    <td colspan="2" class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Total Share:</td>
                    <td class="px-2 py-1 text-center font-semibold" style="color: #100f0d;">
                        {{ number_format($totalShare * 100, 2) . '%' }}
                    </td>
                    <td></td>
                </tr>
            @endif--}}

        </tbody>
    </table>


    <br>




    {{-------------------------------------------------------------------------------}}
    {{-- INSUREDS (grouped by Placement Scheme)                                      --}}
    {{-------------------------------------------------------------------------------}}

    <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px;">
        Insureds
    </h4>

    @php
        $schemeMetaById = collect($costSchemes ?? [])
            ->mapWithKeys(function ($s) {
                $key = $s['cscheme_id'] ?? $s['id'] ?? null;
                return $key ? [
                    $key => [
                        'label' => $s['id'] ?? '—',
                        'share' => (float) ($s['share'] ?? 0),
                    ],
                ] : [];
            });

        $schemeKey = fn ($i) => $i['cscheme_id'] ?? $i['cost_scheme_id'] ?? '—';

        $insuredsGrouped = collect($insureds ?? [])->groupBy($schemeKey);
    @endphp

    {{-- ✅ NEW: un solo wrapper para TODAS las tablas --}}
    <div class="overflow-x-auto"> {{-- ✅ NEW --}}
        <div class="min-w-[1200px]"> {{-- ✅ NEW: ancho base común para todo --}}
            @forelse ($insuredsGrouped as $schemeId => $rows)
                @php
                    $meta        = $schemeMetaById[$schemeId] ?? null;
                    $schemeLabel = $meta['label'] ?? $schemeId;
                    $schemeShare = (float) ($meta['share'] ?? 0);

                    $countInsureds = $rows->unique(fn($i) => $i['company']['name'] ?? null)->count();

                    $totalAllocation = $rows->sum('allocation_percent');
                    $totalPremium    = $rows->sum('premium');
                    $totalFtp        = $rows->sum(fn($i) => $i['premium_ftp'] ?? 0);
                    $totalFts        = $rows->sum(fn($i) => $i['premium_fts'] ?? 0);
                @endphp

                <div class="px-2 py-1 text-left font-semibold mt-4 text-sm" style="color: #100f0d;">
                    Placement Scheme: <span class="font-bold">{{ $schemeLabel }}</span>
                </div>

                <table
                    class="w-full text-sm border-separate border-spacing-y-1 mt-2 table-fixed"  {{-- ✅ NEW: table-fixed --}}
                    style="table-layout: fixed; width: 100%;" {{-- ✅ NEW: forzado --}}
                >
                    <colgroup>
                        <col style="width:4%;">   {{-- # --}}
                        <col style="width:22%;">  {{-- Insured --}}
                        <col style="width:22%;">  {{-- Coverage --}}
                        <col style="width:8%;">   {{-- Share --}}
                        <col style="width:8%;">   {{-- Country --}}
                        <col style="width:9%;">   {{-- Allocation --}}
                        <col style="width:9%;">   {{-- Annual Premium --}}
                        <col style="width:9%;">   {{-- Annual Premium Ftp --}}
                        <col style="width:9%;">   {{-- Annual Premium Fts --}}
                    </colgroup>

                    <thead>
                        <tr class="border-b border-gray-600">
                            <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">#</th>
                            <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">Insured</th>
                            <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">Coverage</th>
                            <th class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Share</th>
                            <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">Country</th>
                            <th class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Allocation</th>
                            <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d;">Annual<br>Premium</th>
                            <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d;">Annual<br>Premium Ftp</th>
                            <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d;">Annual<br>Premium Fts</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rows->values() as $index => $insured)
                            <tr class="bg-gray-800 rounded text-gray-300 border-b border-gray-600">
                                <td class="px-2 py-1">{{ $index + 1 }}</td>

                                <td class="px-2 py-1 truncate" title="{{ $insured['company']['name'] ?? '-' }}">
                                    {{ $insured['company']['name'] ?? '-' }}
                                </td>

                                <td class="px-2 py-1 truncate" title="{{ $insured['coverage']['name'] ?? '-' }}">
                                    {{ $insured['coverage']['name'] ?? '-' }}
                                </td>

                                <td class="px-2 py-1 text-right">
                                    {{ number_format($schemeShare * 100, 2) . '%' }}
                                </td>

                                <td class="px-2 py-1 truncate" title="{{ $insured['company']['country']['name'] ?? '-' }}">
                                    {{ $insured['company']['country']['name'] ?? '-' }}
                                </td>

                                <td class="px-2 py-1 text-right">
                                    {{ isset($insured['allocation_percent']) ? number_format($insured['allocation_percent'] * 100, 2) . '%' : '-' }}
                                </td>

                                <td class="px-2 py-1 text-right whitespace-nowrap">
                                    ${{ number_format($insured['premium'] ?? 0, 2) }}
                                </td>

                                <td class="px-2 py-1 text-right whitespace-nowrap">
                                    ${{ number_format($insured['premium_ftp'] ?? 0, 2) }}
                                </td>

                                <td class="px-2 py-1 text-right whitespace-nowrap">
                                    ${{ number_format($insured['premium_fts'] ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach

                        <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                            <td class="px-2 py-1 font-semibold" style="color:#100f0d;">{{ $countInsureds }}</td>
                            <td class="px-2 py-1 font-semibold" style="color:#100f0d;">
                                {{ $countInsureds === 1 ? 'insured' : 'insureds' }}
                            </td>
                            <td class="px-2 py-1"></td>
                            <td class="px-2 py-1"></td>

                            <td class="px-2 py-1 text-right font-semibold" style="color:#100f0d;">Totals:</td>
                            <td class="px-2 py-1 text-right font-semibold" style="color:#100f0d;">
                                {{ number_format($totalAllocation * 100, 2) . '%' }}
                            </td>
                            <td class="px-2 py-1 text-right font-semibold whitespace-nowrap" style="color:#100f0d;">
                                ${{ number_format($totalPremium, 2) }}
                            </td>
                            <td class="px-2 py-1 text-right font-semibold whitespace-nowrap" style="color:#100f0d;">
                                ${{ number_format($totalFtp, 2) }}
                            </td>
                            <td class="px-2 py-1 text-right font-semibold whitespace-nowrap" style="color:#100f0d;">
                                ${{ number_format($totalFts, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                 <br>
            @empty
                <div class="px-2 py-2 text-center text-gray-400">No insureds available</div>
            @endforelse
        </div>
    </div>



    <br>


    {{-------------------------------------------------------------------------------}}
    {{-- COSTS BREAKDOWN                                                           --}}
    {{-------------------------------------------------------------------------------}}
    @php
        // Agrupamos los nodos por share
        $groupedByShare = collect($costNodes ?? [])->groupBy(fn ($node) => $node->costScheme->share ?? 0); //Cambio
        $groupIndex = 1;
    @endphp

    {{-- COSTS BREAKDOWN --}}
     <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px;">
       Costs Breakdown
    </h4>


    
    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <thead>
            
            <tr>
                <th class="px-2 py-1 text-left text-gray-400"></th>
                <th class="px-2 py-1 text-left text-gray-400"></th>
                {{-- <th class="px-2 py-1 text-left text-gray-400"></th> --}}
                <th class="px-2 py-1 text-left text-gray-400"></th> 
                <th class="px-2 py-1 text-right text-gray-400"></th>
                <th class="px-2 py-1 text-right align-middle font-semibold font-medium" style="color: #100f0d;">Orig. Curr.</th>
                <th class="px-2 py-1 text-right align-middle font-semibold font-medium" style="color: #100f0d;">US Dollars</th>
            </tr>

        </thead>

        <tbody>

            <tr class="bg-gray-900 text-gray-300 font-semibold">
                <td colspan="4" class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Gross Underwritten Premium</td>
                <td class="px-2 py-1 text-right border-t border-gray-600 font-semibold" style="color: #100f0d;">${{ number_format($totalPremiumFts ?? 0, 2) }}</td>
                <td class="px-2 py-1 text-right border-t border-gray-600 font-semibold" style="color: #100f0d;">${{ number_format($totalConvertedPremium ?? 0, 2) }}</td>
            </tr>

            {{--<tr>
                <td colspan="6" class="px-0 py-1">
                    <div class="border-t border-gray-600 w-full h-px"></div>
                </td>
            </tr> --}}




            <tr><td colspan="6" class="py-2"></td></tr>


            {{-- Table headers for each group --}}
                <tr class="text-sm text-gray-300 uppercase">
                    <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">#</th>
                    <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">Partner</th>
                    {{-- <th class="px-2 py-1 text-left">Share</th> --}}
                    <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">Concept</th>
                    <th class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Value</th>
                    <th class="px-2 py-1 text-right"></th>
                    <th class="px-2 py-1 text-right"></th>
                </tr>





            @forelse ($groupedCostNodes ?? [] as $group)

                {{-- <tr>
                    <td colspan="7">
                        <div class="border-t border-gray-600 my-2"></div>
                    </td>
                </tr> --}}

                {{-- Table headers for each group 
                <tr class="text-sm text-gray-300 uppercase">
                    <th class="px-2 py-1 text-left">#</th>
                    <th class="px-2 py-1 text-left">Partner</th>
                    <th class="px-2 py-1 text-left">Share</th>
                    <th class="px-2 py-1 text-left">Concept</th>
                    <th class="px-2 py-1 text-right">Value</th>
                    <th class="px-2 py-1 text-right"></th>
                    <th class="px-2 py-1 text-right"></th>
                </tr> --}}

                <tr>
                    <td colspan="6" class="px-0 py-1">
                        <div class="border-t border-gray-600 w-full h-px"></div>
                    </td>
                </tr>

                {{-- Deduction detail rows --}}
                @foreach ($group['nodes'] as $node)
                    <tr class="bg-gray-800 text-gray-300 border-b border-gray-600">
                        <td class="px-2 py-1">{{ $node['index'] }}</td>
                        <td class="px-2 py-1">{{ $node['partner'] ?? '-' }}</td>                    <!-- VARIABLE NUEVA -->
                       {{-- <td class="px-2 py-1">{{ number_format($node['share'] * 100, 2) }}%</td> --}}
                        <td class="px-2 py-1">{{ $node['deduction'] ?? '-' }}</td>
                        <td class="px-2 py-1 text-right">{{ number_format($node['value'] * 100, 2) }}%</td>
                        <td class="px-2 py-1 text-right">${{ number_format($node['deduction_amount']* -1, 2) }}</td>
                        <td class="px-2 py-1 text-right">${{ number_format($node['deduction_usd']* -1, 2) }}</td>
                    </tr>
                @endforeach

                {{-- Subtotal row BELOW each group --}}
                <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                    <td colspan="3" class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">
                        Share {{ number_format($group['share'] * 100, 2) }}%.
                    </td> 
                    <td class="px-2 py-1 text-right text-gray-300 font-semibold" style="color: #100f0d;">Subtotal:</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">${{ number_format($group['subtotal_orig']* -1, 2) }}</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">${{ number_format($group['subtotal_usd']* -1, 2) }}</td>
                </tr>

            @empty
                <tr>
                    <td colspan="3" class="px-2 py-2 text-center text-gray-400 font-semibold" style="color: #100f0d;">No cost nodes available</td>
                </tr>
            @endforelse


            {{-- Grand total --}}
            @php
                $grandTotalOrig = collect($groupedCostNodes ?? [])->sum('subtotal_orig');
                $grandTotalUsd = collect($groupedCostNodes ?? [])->sum('subtotal_usd');
            @endphp
            <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                <td colspan="4" class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Total Deductions:</td>
                <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">${{ number_format($grandTotalOrig * -1, 2) }}</td>
                <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">${{ number_format($grandTotalUsd * -1, 2) }}</td>
            </tr>
            <tr><td colspan="4" class="py-2"></td></tr>
           <tr class="bg-gray-900 text-gray-300 font-semibold">
                <td colspan="4" class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Net Underwritten Premium</td>
                <td class="px-2 py-1 text-right border-t border-gray-600 font-semibold" style="color: #100f0d;">${{ number_format($totalPremiumFts - $grandTotalOrig?? 0, 2) }}</td>
                <td class="px-2 py-1 text-right border-t border-gray-600 font-semibold" style="color: #100f0d;">${{ number_format($totalConvertedPremium - $grandTotalUsd ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>


    {{-------------------------------------------------------------------------------}}
    {{-- INSTALLMENTS                                                              --}}
    {{-------------------------------------------------------------------------------}}
     <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px;">
       Installments
    </h4>


    <table class="w-full text-sm border-collapse mt-2">
        <thead>
             <tr class="border-b border-gray-600">
                <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d;">#</th>
                <th class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Proportion</th>
                <th class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Exchange Rate</th>
                <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d;">Due Date</th>
                <th class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Orig. Curr.</th>
                <th class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">US Dollars</th>
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
                    
                    $amountUsd = $rate > 0 ? ($amountOrig / $rate) : 0;

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
                {{-- Fila separadora manual --}}
                <tr>
                    <td colspan="6" class="px-0 py-1">
                        <div class="border-t border-gray-600 w-full h-px"></div>
                    </td>
                </tr>

                {{-- Fila de totales --}}
                <tr class="bg-gray-800 text-gray-300 font-semibold">
                    <td colspan="4" class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">Total:</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">${{ number_format($grandOrig, 2) }}</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d;">${{ number_format($grandUsd, 2) }}</td>
                </tr>
            @endif

        </tbody>
    </table>


    {{-------------------------------------------------------------------------------}}
    {{-- INSTALLMENTS LOGS                                                         --}}
    {{-------------------------------------------------------------------------------}}
     <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px;">
       Installments Log
    </h4>


     <table class="w-full text-sm border-collapse mt-2">
        <thead>
             <tr class="border-b border-gray-600">
                <th class="px-2 py-1 text-left font-semibold"  style="color: #100f0d;">#</th>
                <th class="px-2 py-1 text-left font-semibold"  style="color: #100f0d;">Deduction</th>
                <th class="px-2 py-1 text-left font-semibold"  style="color: #100f0d;">Source</th>
                <th class="px-2 py-1 text-left font-semibold"  style="color: #100f0d;">Destination</th>
                <th class="px-2 py-1 text-right font-semibold"  style="color: #100f0d;">Exchange Rate</th>
                <th class="px-2 py-1 text-right font-semibold"  style="color: #100f0d;">Gross Amount</th>
                <th class="px-2 py-1 text-right font-semibold"  style="color: #100f0d;">Discount</th>
                <th class="px-2 py-1 text-right font-semibold"  style="color: #100f0d;">Banking Fee</th>
                <th class="px-2 py-1 text-right font-semibold"  style="color: #100f0d;">Net Amount</th>
                <th class="px-2 py-1 text-right font-semibold"  style="color: #100f0d;">Status</th>
             </tr>
        </thead>

        <tbody>
            @php
                $nodesFlat = collect($groupedCostNodes ?? [])
                    ->flatMap(fn ($g) => $g['nodes'] ?? [])
                    ->sortBy('index')
                    ->values();

                $logsByTxn = collect($logsByTxn ?? []); // 👈 viene del viewData
            @endphp

            @if (empty($transactions) || $nodesFlat->isEmpty())
                <tr>
                    <td colspan="10" class="px-2 py-2 text-center text-gray-400">
                        No installments or cost nodes to display
                    </td>
                </tr>
            @else
                @foreach (($transactions ?? []) as $tIdx => $txn)
                    @foreach ($nodesFlat as $nIdx => $node)
                        @php
                            $num       = ($tIdx + 1) . '.' . ($nIdx + 1);
                            $rate      = isset($txn['exch_rate']) ? (float) $txn['exch_rate'] : null;

                            // match por transacción persistida + índice del log (mismo que el del nodo)
                            $txnId     = $txn['id'] ?? null;
                            $nodeIndex = (int)($node['index'] ?? ($nIdx + 1));

                            $logRow    = $txnId ? ($logsByTxn[$txnId][$nodeIndex] ?? null) : null;

                            $destination = $logRow['to_short']
                                            ?? ($node['partner_short'] ?? $node['partner'] ?? '-');
                        @endphp
                        
                        @php
                            // Normaliza proportion: si viene 50 => 0.5; si ya viene 0.5 => 0.5
                            $pRaw = (float) ($txn['proportion'] ?? 0);
                            $prop = $pRaw > 1 ? $pRaw / 100 : $pRaw;

                            // Toma los valores del log (si existen) y escálalos por la proportion
                            $grossScaled    = isset($logRow['gross'])    ? $logRow['gross']    * $prop : null;
                            $discountScaled = isset($logRow['discount']) ? $logRow['discount'] * $prop : null;
                            $bankingScaled  = isset($logRow['banking'])  ? $logRow['banking']  * $prop : null;

                            // Si net_amount es columna generada en DB, puedes mostrar:
                            //   a) el net del registro *prop (vista previa proporcional), o
                            //   b) el net directo del registro (sin escalar) si prefieres ver el valor real guardado.
                            // Aquí lo dejamos escalado para ser consistente con las otras columnas:
                            $netScaled      = isset($logRow['net'])      ? $logRow['net']      * $prop : null;
                        @endphp

                        <tr class="bg-gray-800 text-gray-300 border-b border-gray-700">
                            <td class="px-2 py-1">{{ $num }}</td>

                            <td class="px-2 py-1 text-left">
                                {{ $node['deduction'] ?? '-' }}
                            </td>

                            <td class="px-2 py-1 text-left">
                                {{ $node['partner_short'] ?? $node['partner'] ?? '-' }}
                            </td>

                            {{-- DESTINATION: usa to_entity->short_name si existe, si no cae a partner_short --}}
                            <td class="px-2 py-1 text-center">
                                {{ $destination }}
                            </td>

                            <td class="px-2 py-1 text-right">
                                {{ $rate !== null ? number_format($rate, 5) : '-' }}
                            </td>

                            
                            {{-- Las demás columnas pueden usar también $logRow si quieres mostrar valores reales cuando existan --}}
                            <td class="px-2 py-1 text-right">
                                {{ $grossScaled !== null ? number_format($grossScaled, 2) : '—' }}
                            </td>
                            <td class="px-2 py-1 text-right">
                                {{ $discountScaled !== null ? number_format($discountScaled, 2) : '—' }}
                            </td>
                            <td class="px-2 py-1 text-right">
                                {{ $bankingScaled !== null ? number_format($bankingScaled, 2) : '—' }}
                            </td>


                            <td class="px-2 py-1 text-right">
                                {{ $netScaled !== null ? number_format($netScaled, 2) : '—' }}
                            </td>


                            <td class="px-2 py-1 text-right">
                                <span class="uppercase text-xs tracking-wide">
                                    {{ $logRow['status'] ?? 'preview' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @endif
        </tbody>    


    </table>









</div>
