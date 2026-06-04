{{-- ✅ PRINT ROOT WRAPPER --}}
<div id="summary-print-root"
     class="border rounded p-4 space-y-4"
     style="
        background-color:#f1efea;
        color:#1f262a;
        padding: 24px;
        font-family:'Montserrat', sans-serif;

        /* 👇 clave: el reporte usa el alto del viewport */
        max-height: calc(100vh - 220px);
        overflow: auto;
     ">

    {{-- ⚠️ RECOMENDACIÓN: evita <head> dentro del blade.
         Si lo dejas, al menos que sea @once para no duplicarlo --}}
    @once
        <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    @endonce

    {{-- MAIN TITTLE --}}
    <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size:15px; 
font-weight: 600;">
        <span class="px-2 py-1 text-left font-extrabold text-gray-300 w-1/4"></span>
        <span class="px-2 py-1 w-1/4">{{ $id ?? '-' }}</span>
    </h4>
    
     {{--CURRENT DATE 
    <div class="text-right text-sm font-medium text-gray-600 mb-2" style="border-bottom: 1px solid #100f0d;">
        Date: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div> --}}
    <br>
    {{-------------------------------------------------------------------------------}}
    {{-- DOCUMENT DETAILS SECTION                                                  --}}
    {{-------------------------------------------------------------------------------}}
    <h4 class="mt-6 mb-4" style="color: #db4a2b; font-size: 15px; 
font-weight: 600;">
       General Details
    </h4>

    <table class="table-fixed w-full text-sm border-separate border-spacing-0">
        <colgroup>
            {{-- 4 columnas con datos → auto --}}
            <col style="width:25%;">
            <col style="width:25%;">
            <col style="width:25%;">
            <col style="width:25%;">

            {{-- 2 columnas dummy con ancho fijo --}}
            <col style="width: 150px;">
            <col style="width: 150px;">
        </colgroup>

        <tbody>
            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-left" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                    Document type:
                </td>
                <td class="px-2 py-1 font-thin" style="border-bottom: 1px solid #100f0d; font-weight: 100;">{{ $documentType ?? '-' }}</td>
                <td class="px-2 py-1 text-left" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                    Creation date:
                </td>
                <td class="px-2 py-1" style="border-bottom: 1px solid #100f0d;">
                    {{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d/m/Y') : '-' }}
                </td>
                {{-- columnas “dummy” --}}
                <td></td>
                <td></td>
            </tr>

            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-left" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                    Premium type:
                </td>
                <td class="px-2 py-1 font-thin" style="border-bottom: 1px solid #100f0d; font-weight: 100;">{{ $premiumType ?? '-' }}</td>
                <td class="px-2 py-1 text-left" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                    Period:
                </td>
                <td class="px-2 py-1 font-thin" style="border-bottom: 1px solid #100f0d;font-weight: 100;">
                    {{ $inceptionDate ? \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') : '-' }}
                    to
                    {{ $expirationDate ? \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') : '-' }}
                </td>
                <td></td>
                <td></td>
            </tr>

            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-left" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                    Original currency:
                </td>
                <td class="px-2 py-1 font-thin" style="border-bottom: 1px solid #100f0d; font-weight: 100;">{{ $originalCurrency ?? '-' }}</td>
                <td class="px-2 py-1 text-left" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                    Coverage days:
                </td>
                <td class="px-2 py-1 font-thin" style="border-bottom: 1px solid #100f0d; font-weight: 100;">
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
     <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px; 
font-weight: 600;">
       Placement Schemes
    </h4>

    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2">
        <colgroup>
                <col style="width:5%;">
                <col style="width:20%;">
                <col style="width:45%;">
                <col style="width:20%;">
                <col style="width:20%;">
            </colgroup>
        <thead>
             <tr class="border-b text-gray-300 border-gray-600">
                <th class="px-2 py-1 text-left font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; color: #100f0d; text-align:left">#</th>
                <th class="px-2 py-1 text-left font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; color: #100f0d; text-align:left">Id</th>
                <th class="px-2 py-1 text-left font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; color: #100f0d; text-align:left">Description</th>
                <th class="px-2 py-1 text-center font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; color: #100f0d; text-align:left">Share (%)</th>
                <th class="px-2 py-1 text-center font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; color: #100f0d; text-align:left">Agreement Type</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($costSchemes ?? [] as $index => $scheme)
                <tr class="bg-gray-800 rounded text-gray-300 border-b border-gray-600">

                    <td class="px-2 py-1" style="border-bottom: 1px solid #100f0d;">{{ $index + 1 }}</td>
                    <td class="px-2 py-1" style="border-bottom: 1px solid #100f0d;">{{ $scheme['id'] ?? '-' }}</td>

                    <td class="px-2 py-1" style="border-bottom: 1px solid #100f0d;"> {{-- ✅ MOD [PS-DESC-3] NEW --}}
                        {{ $scheme['description'] ?? '-' }}
                    </td>

                    <td class="px-2 py-1 text-center" style="border-bottom: 1px solid #100f0d;">
                        {{ isset($scheme['share']) ? number_format($scheme['share'] * 100, 2) . '%' : '-' }}
                    </td>
                    <td class="px-2 py-1 text-center" style="border-bottom: 1px solid #100f0d;">{{ $scheme['agreement_type'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-2 py-2 text-center text-gray-400">No cost schemes available</td>
                </tr>
            @endforelse

            {{-- 🔹 TOTAL ROW 
            @if (isset($totalShare))
                 <tr class="border-t border-gray-700 bg-gray-800 text-gray-300 font-semibold">
                    <td colspan="2" class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600;">Total Share:</td>
                    <td class="px-2 py-1 text-center font-semibold" style="color: #100f0d; font-weight: 600;">
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

    <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px; 
font-weight: 600;">
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

                <div class="px-2 py-1 text-left font-semibold mt-4 text-sm" style="color: #100f0d; font-weight: 600;">
                    Placement Scheme: <span>{{ $schemeLabel }}</span>
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
                            <th class="px-2 py-1 text-left font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:left; color: #100f0d;">#</th>
                            <th class="px-2 py-1 text-left font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:left; color: #100f0d;">Insured</th>
                            <th class="px-2 py-1 text-left font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:left; color: #100f0d;">Coverage</th>
                            <th class="px-2 py-1 text-right font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:center; color: #100f0d;">Share</th>
                            <th class="px-2 py-1 text-left font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:right; color: #100f0d;">Country</th>
                            <th class="px-2 py-1 text-right font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:center; color: #100f0d;">Allocation</th>
                            <th class="px-2 py-1 text-center font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:center; color: #100f0d;">Annual<br>Premium</th>
                            <th class="px-2 py-1 text-center font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align: center; color: #100f0d;">Annual<br>Premium Ftp</th>
                            <th class="px-2 py-1 text-center font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align: center; color: #100f0d;">Annual<br>Premium Fts</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rows->values() as $index => $insured)
                            <tr class="bg-gray-800 rounded text-gray-300 border-b border-gray-600">
                                <td class="px-2 py-1" style="border-bottom: 1px solid #100f0d;">{{ $index + 1 }}</td>

                                <td class="px-2 py-1 truncate" style="border-bottom: 1px solid #100f0d;" title="{{ $insured['company']['name'] ?? '-' }}">
                                    {{ $insured['company']['name'] ?? '-' }}
                                </td>

                                <td class="px-2 py-1 truncate" style="border-bottom: 1px solid #100f0d; text-align:left;" title="{{ $insured['coverage']['name'] ?? '-' }}">
                                    {{ $insured['coverage']['name'] ?? '-' }}
                                </td>

                                <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d; text-align:center;">
                                    {{ number_format($schemeShare * 100, 2) . '%' }}
                                </td>

                                <td class="px-2 py-1 truncate" style="border-bottom: 1px solid #100f0d; text-align:center;" title="{{ $insured['company']['country']['name'] ?? '-' }}">
                                    {{ $insured['company']['country']['name'] ?? '-' }}
                                </td>

                                <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d; text-align:center;">
                                    {{ isset($insured['allocation_percent']) ? number_format($insured['allocation_percent'] * 100, 2) . '%' : '-' }}
                                </td>

                                <td class="px-2 py-1 text-right whitespace-nowrap" style="border-bottom: 1px solid #100f0d; text-align:right; white-space: nowrap;">
                                    ${{ number_format($insured['premium'] ?? 0, 2) }}
                                </td>

                                <td class="px-2 py-1 text-right whitespace-nowrap" style="border-bottom: 1px solid #100f0d; text-align:right; white-space: nowrap;">
                                    ${{ number_format($insured['premium_ftp'] ?? 0, 2) }}
                                </td>

                                <td class="px-2 py-1 text-right whitespace-nowrap" style="border-bottom: 1px solid #100f0d; text-align:right; white-space: nowrap;">
                                    ${{ number_format($insured['premium_fts'] ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach

                        <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                            <td class="px-2 py-1 font-semibold" style="color:#100f0d; font-weight: 600;">{{ $countInsureds }}</td>
                            <td class="px-2 py-1 font-semibold" style="color:#100f0d; font-weight: 600;">
                                {{ $countInsureds === 1 ? 'insured' : 'insureds' }}
                            </td>
                            <td class="px-2 py-1"></td>
                            <td class="px-2 py-1"></td>

                            <td class="px-2 py-1 text-right font-semibold" style="color:#100f0d; font-weight: 600;">Totals:</td>
                            <td class="px-2 py-1 text-right font-semibold" style="color:#100f0d; font-weight: 600;">
                                {{ number_format($totalAllocation * 100, 2) . '%' }}
                            </td>
                            <td class="px-2 py-1 text-right font-semibold whitespace-nowrap" style="color:#100f0d; font-weight: 600; text-align:right; white-space: nowrap;">
                                ${{ number_format($totalPremium, 2) }}
                            </td>
                            <td class="px-2 py-1 text-right font-semibold whitespace-nowrap" style="color:#100f0d; font-weight: 600; text-align:right; white-space: nowrap;">
                                ${{ number_format($totalFtp, 2) }}
                            </td>
                            <td class="px-2 py-1 text-right font-semibold whitespace-nowrap" style="color:#100f0d; font-weight: 600; text-align:right; white-space: nowrap;">
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
     <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px; font-weight: 600;">
       Costs Breakdown
    </h4>


    
    <table class="w-full text-sm border-separate border-spacing-y-1 mt-2"
        style="table-layout: fixed; width: 100%; border-collapse: collapse;"
    >
        <colgroup>
            <col style="width:4%;">
            <col style="width:60%;">
            
            <col style="width:8%;">
            <col style="width:8%;">
            <col style="width:10%;">
            <col style="width:10%;">
        </colgroup>
        <thead>
            <tr>
                <th class="px-2 py-1 text-left text-gray-400"></th>
                <th class="px-2 py-1 text-left text-gray-400"></th>
                
                <th class="px-2 py-1 text-left text-gray-400"></th> 
                <th class="px-2 py-1 text-right text-gray-400"></th>
                <th class="px-2 py-1" style="color: #100f0d; font-weight: 600; padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">Orig. Curr.</th>
                <th class="px-2 py-1" style="color: #100f0d; font-weight: 600; padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">US Dollars</th> 
            </tr>

        </thead>

        <tbody>

            <tr class="bg-gray-900 text-gray-300 font-semibold">
                <td colspan="4" class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">Gross Underwritten Premium</td>
                <td class="px-2 py-1 border-gray-600 font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">${{ number_format($totalPremiumFts ?? 0, 2) }}</td>
                <td class="px-2 py-1 border-gray-600 font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">${{ number_format($totalConvertedPremium ?? 0, 2) }}</td> 
            </tr>

            <tr><td colspan="6" class="py-2"></td></tr>

            <tr class="text-sm text-gray-300 uppercase">
                <th class="px-2 py-1 font-semibold" style="color: #100f0d; border-bottom: 1px solid #100f0d; text-align:left;">#</th>
                <th class="px-2 py-1 font-semibold" style="color: #100f0d; border-bottom: 1px solid #100f0d; text-align:left;">Partner</th>
                
                <th class="px-2 py-1 font-semibold" style="color: #100f0d; border-bottom: 1px solid #100f0d; text-align:left;">Concept</th>
                <th class="px-2 py-1  font-semibold" style="color: #100f0d; border-bottom: 1px solid #100f0d; text-align:left;">Value</th>
                <th class="px-2 py-1" style="color: #100f0d; border-bottom: 1px solid #100f0d;"></th>
                <th class="px-2 py-1" style="color: #100f0d; border-bottom: 1px solid #100f0d;"></th>
                <th class="px-2 py-1 text-right"></th>
            </tr>

            @forelse ($groupedCostNodes ?? [] as $group)

                <tr>
                    <td colspan="6" style="padding-left: 0px; padding-right: 0px; padding-top: 0.25rem; padding-bottom: 0.25rem;">
                        <div class="border-t border-gray-600 w-full h-px" style="height: 1px; width: 100%;"></div>
                    </td>
                </tr>

                {{-- Deduction detail rows --}}
                @foreach ($group['nodes'] as $node)
                    <tr class="bg-gray-800 text-gray-300 border-b border-gray-600">
                        <td class="px-2 py- 1" style="border-bottom: 1px solid #100f0d;">{{ $node['index'] }}</td>
                        <td class="px-2 py-1" style="border-bottom: 1px solid #100f0d;">{{ $node['partner'] ?? '-' }}</td>                    <!-- VARIABLE NUEVA -->
                        
                        <td class="px-2 py-1" style="border-bottom: 1px solid #100f0d;">{{ $node['deduction'] ?? '-' }}</td>
                        <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d; text-align:right;">{{ number_format($node['value'] * 100, 2) }}%</td>
                        <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d; text-align:right;">${{ number_format($node['deduction_amount']* -1, 2) }}</td>
                        <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d; text-align:right;">${{ number_format($node['deduction_usd']* -1, 2) }}</td> 
                    </tr>
                @endforeach

                {{-- Subtotal row BELOW each group --}}
                <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                    <td colspan="3" class="px-2 py-1 text-left font-semibold" style="color: #100f0d; font-weight: 600;">
                        Share {{ number_format($group['share'] * 100, 2) }}%.
                    </td> 
                    <td class="px-2 py-1 text-right text-gray-300 font-semibold" style="color: #100f0d; font-weight: 600; text-align:right;">Subtotal:</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align:right;">${{ number_format($group['subtotal_orig']* -1, 2) }}</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align:right;">${{ number_format($group['subtotal_usd']* -1, 2) }}</td>
                </tr>

            @empty
                <tr>
                    <td colspan="2" class="px-2 py-2 text-center text-gray-400 font-semibold" style="color: #100f0d; font-weight: 600;">No cost nodes available</td>
                </tr>
            @endforelse


            {{-- Grand total --}}
            @php
                $grandTotalOrig = collect($groupedCostNodes ?? [])->sum('subtotal_orig');
                $grandTotalUsd = collect($groupedCostNodes ?? [])->sum('subtotal_usd');
            @endphp
            <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                <td colspan="4" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">Total Deductions:</td>
                <td style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem;">${{ number_format($grandTotalOrig * -1, 2) }}</td>
                <td style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem;">${{ number_format($grandTotalUsd * -1, 2) }}</td> 
            </tr>
            <tr><td colspan="4" style="padding-top: 0.5rem; padding-bottom: 0.5rem;"></td></tr>
           <tr class="bg-gray-900 text-gray-300 font-semibold">
                <td colspan="4" class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">Net Underwritten Premium</td>
                <td class="px-2 py-1 text-right border-t border-gray-600 font-semibold" style="border-top: 1px solid #100f0d; color: #100f0d; font-weight: 600; text-align:right;">${{ number_format($totalPremiumFts - $grandTotalOrig?? 0, 2) }}</td>
                <td class="px-2 py-1 text-right border-t border-gray-600 font-semibold" style="border-top: 1px solid #100f0d; color: #100f0d; font-weight: 600; text-align:right;">${{ number_format($totalConvertedPremium - $grandTotalUsd ?? 0, 2) }}</td> 
            </tr>
        </tbody>
    </table>
    <br>
    <div style="height: 32px;"></div>
    {{-------------------------------------------------------------------------------}}
    {{-- INSTALLMENTS                                                              --}}
    {{-------------------------------------------------------------------------------}}
    
    <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px; font-weight: 600;">
       Transactions
    </h4>


    <table class="w-full text-sm border-collapse mt-2" style="table-layout: fixed; width: 100%; border-collapse: collapse;">
        <colgroup>
            <col style="width:5%;">  
            <col style="width:20%;">  
            <col style="width:20%;">  
            <col style="width:20%;">
            <col style="width:20%;">
            <col style="width:10%;">   
        </colgroup>
        <thead>
             <tr class="border-b border-gray-600">
                <th class="px-2 py-1 text-left font-semibold" style="text-align: left; color: #100f0d; border-bottom: 1px solid #100f0d;">#</th>
                <th class="px-2 py-1 text-right font-semibold" style="text-align: right; color: #100f0d; border-bottom: 1px solid #100f0d;">Proportion</th>
                <th class="px-2 py-1 text-right font-semibold" style="text-align: right; color: #100f0d; border-bottom: 1px solid #100f0d;">Exchange Rate</th>
                <th class="px-2 py-1 text-center font-semibold" style="text-align: center; color: #100f0d; border-bottom: 1px solid #100f0d;">Due Date</th>
                <th class="px-2 py-1 text-right font-semibold" style="text-align: right; color: #100f0d; border-bottom: 1px solid #100f0d;">Orig. Curr.</th>
                <th class="px-2 py-1 text-right font-semibold" style="text-align: right; color: #100f0d; border-bottom: 1px solid #100f0d;">US Dollars</th>
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
                    $proportion = floatval($txn['proportion'] ?? 0);
                    $rate = floatval($txn['exch_rate'] ?? 0);
                    $dueDate = $txn['due_date'] ?? null;

                    $amountOrig = $netPremium * $proportion;
                    
                    $amountUsd = $rate > 0 ? ($amountOrig / $rate) : 0;

                    $grandOrig += $amountOrig;
                    $grandUsd += $amountUsd;
                @endphp

                <tr class="bg-gray-800 text-gray-300 border-b border-gray-700">
                   <td class="px-2 py-1">{{ $loop->iteration }}</td>
                    <td class="px-2 py-1 text-right" style="text-align: right;">{{ number_format($proportion * 100, 2) }}%</td>
                    <td class="px-2 py-1 text-right" style="text-align: right;">{{ number_format($rate, 4) }}</td>
                    <td class="px-2 py-1 text-center" style="text-align: center;">
                        {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-2 py-1 text-right" style="text-align: right;">${{ number_format($amountOrig, 2) }}</td>
                    <td class="px-2 py-1 text-right" style="text-align: right;">${{ number_format($amountUsd, 2) }}</td>
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
                    <td colspan="4" class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">Total:</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem;">${{ number_format($grandOrig, 2) }}</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem;">${{ number_format($grandUsd, 2) }}</td>
                </tr>
            @endif

        </tbody>
    </table>
    
<br>
    {{--============================================================================--}}
    {{-- INSTALLMENTS LOGS (one table per transaction)                               --}}
    {{--============================================================================--}}
    <br>
    <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px; 
font-weight: 600;">
        Transactions Lifecycle
    </h4>
    <br>
    @php
        $nodesFlat = collect($groupedCostNodes ?? [])
            ->flatMap(fn ($g) => $g['nodes'] ?? [])
            ->sortBy('index')
            ->values();

        $logsByTxn = collect($logsByTxn ?? []);
        $transactions = collect($transactions ?? []);
    @endphp

    @if ($transactions->isEmpty() || $nodesFlat->isEmpty())
        <div class="px-2 py-2 text-center text-gray-400">
            No installments or cost nodes to display
        </div>
    @else

        @foreach ($transactions as $tIdx => $txn)
            @php
                $txnId = $txn['id'] ?? null;

                // Normaliza proportion para mostrar 55.00% aunque venga 0.55
                $pRaw = (float) ($txn['proportion'] ?? 0);
                $pDec = $pRaw > 1 ? $pRaw / 100 : $pRaw;
                $pPct = $pDec * 100;

                $dueDate  = $txn['due_date'] ?? null;
                $exchRate = $txn['exch_rate'] ?? null;

                // Logs del installment actual (colección indexada por nodeIndex)
                $txnLogs = $txnId ? collect($logsByTxn[$txnId] ?? []) : collect();
            @endphp

            {{-- Subtítulo por Installment --}}
            <br>
            <div class="mt-4 mb-2 text-sm" style="color:#100f0d;">
                <span class="font-semibold" style="font-weight: 600;">Installment {{ $txn['index'] ?? ($tIdx + 1) }}</span>
                <span class="ml-2 text-gray-500">
                    (Proportion: {{ number_format($pPct, 2) }}%, 
                    Exch. Rate: {{ $exchRate !== null ? number_format((float)$exchRate, 4) : '-' }},
                    Due: {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d/m/Y') : '-' }})
                </span>
            </div>
            <br>
            <table class="w-full text-sm border-collapse mt-2">
                <colgroup>
                <col style="width:5%;">
                <col style="width:5%;">  
                <col style="width:20%;">
                <col style="width:20%;">
                <col style="width:10%;">
                <col style="width:10%;">
                <col style="width:5%;">
                <col style="width:5%;">
                <col style="width:10%;">
                <col style="width:10%;">
            </colgroup>
                <thead>
                    <tr class="border-b border-gray-600" style="table-layout: fixed; width: 100%; border-collapse: collapse;">
                        <th class="px-2 py-1 text-left font-semibold"  style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:left; color: #100f0d; border-bottom: 1px solid #100f0d;">#</th>
                        <th class="px-2 py-1 text-left font-semibold"  style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:left; color: #100f0d; border-bottom: 1px solid #100f0d;">Deduction</th>
                        <th class="px-2 py-1 text-left font-semibold"  style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:left; color: #100f0d; border-bottom: 1px solid #100f0d;">Source</th>
                        <th class="px-2 py-1 text-left font-semibold"  style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:left; color: #100f0d; border-bottom: 1px solid #100f0d;">Destination</th>
                        <th class="px-2 py-1 text-right font-semibold" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem; text-align:right; color: #100f0d; border-bottom: 1px solid #100f0d;">Exchange Rate</th>
                        <th class="px-2 py-1 text-right font-semibold" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem; text-align:right; color: #100f0d; border-bottom: 1px solid #100f0d;">Gross Amount</th>
                        <th class="px-2 py-1 text-right font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:right; color: #100f0d; border-bottom: 1px solid #100f0d;">Discount</th>
                        <th class="px-2 py-1 text-right font-semibold" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem; text-align:right; color: #100f0d; border-bottom: 1px solid #100f0d;">Banking Fee</th>
                        <th class="px-2 py-1 text-right font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:right; color: #100f0d; border-bottom: 1px solid #100f0d;">Net Amount</th>
                        <th class="px-2 py-1 text-right font-semibold" style="padding-left: 0.5rem; padding-right: 0.5rem; text-align:right; color: #100f0d; border-bottom: 1px solid #100f0d;">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($nodesFlat as $nIdx => $node)
                        @php
                            // Número 1.1, 1.2 ... 2.1, 2.2 ...
                            $num = ($tIdx + 1) . '.' . ($nIdx + 1);

                            $nodeIndex = (int) ($node['index'] ?? ($nIdx + 1));
                            $logRow    = $txnId ? ($txnLogs[$nodeIndex] ?? null) : null;

                            $destination = $logRow['to_short']
                                ?? ($node['partner_short'] ?? $node['partner'] ?? '-');

                            $gross    = $logRow['gross'] ?? null;
                            $discount = $logRow['discount'] ?? null;
                            $banking  = $logRow['banking'] ?? null;
                            $net      = $logRow['net'] ?? null;

                            // mejor tomar la tasa del log si existe, si no cae a la de la transacción
                            $rate = $logRow['exch_rate'] ?? ($exchRate !== null ? (float) $exchRate : null);

                            $status = $logRow['status'] ?? 'preview';
                        @endphp

                        <tr class="bg-gray-800 text-gray-300 border-b border-gray-700">
                            <td class="px-2 py-1">{{ $num }}</td>

                            <td class="px-2 py-1 text-left" style="text-align: left;">
                                {{ $node['deduction'] ?? '-' }}
                            </td>

                            <td class="px-2 py-1 text-left" style="text-align: left;">
                                {{ $node['partner_short'] ?? $node['partner'] ?? '-' }}
                            </td>

                            <td class="px-2 py-1 text-left" style="text-align: left;">
                                {{ $destination }}
                            </td>

                            <td class="px-2 py-1 text-right" style="text-align: right;">
                                {{ $rate !== null ? number_format((float)$rate, 5) : '-' }}
                            </td>

                            <td class="px-2 py-1 text-right" style="text-align: right;">
                                {{ $gross !== null ? number_format((float)$gross, 2) : '—' }}
                            </td>

                            <td class="px-2 py-1 text-right" style="text-align: right;">
                                {{ $discount !== null ? number_format((float)$discount, 2) : '—' }}
                            </td>

                            <td class="px-2 py-1 text-right" style="text-align: right;">
                                {{ $banking !== null ? number_format((float)$banking, 2) : '—' }}
                            </td>

                            <td class="px-2 py-1 text-right" style="text-align: right;">
                                {{ $net !== null ? number_format((float)$net, 2) : '—' }}
                            </td>

                            <td class="px-2 py-1 text-right" style="text-align: right;">
                                <span class="uppercase text-xs tracking-wide">
                                    {{ $status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Separador suave entre tablas --}}
            <div class="mt-4"></div>
        @endforeach

    @endif



        </tbody>    


    </table>

</div>


{{-- ✅ PRINT CSS (agregar al final del blade) --}}
{{-- @once
    <style>
        @media print {

            /* =====================================================
            * 1) IMPRIMIR TODO EL CONTENIDO (sin scroll / sin cortes)
            * ===================================================== */

            /* Quita límites de altura y scroll del modal Filament */
            .fi-modal,
            .fi-modal-window,
            .fi-modal-content,
            .fi-modal-body,
            .fi-modal-content .overflow-y-auto,
            .fi-modal-content [style*="max-height"],
            .fi-modal-content [class*="max-h"],
            .fi-modal-content [class*="overflow-y"],
            .fi-modal-content [class*="overflow-auto"] {
                max-height: none !important;
                height: auto !important;
                overflow: visible !important;
            }

            /* Wrapper principal del reporte */
            #summary-print-root {
                max-height: none !important;
                height: auto !important;
                overflow: visible !important;
                page-break-after: auto !important;
                background: #fff !important;
            }

            /* Cualquier wrapper interno con scroll */
            #summary-print-root .overflow-x-auto,
            #summary-print-root .overflow-y-auto {
                overflow: visible !important;
            }

            /* =====================================================
            * 2) OCULTAR UI (NO debe salir en el PDF)
            * ===================================================== */

            /* Botón Print (tu Action con class="no-print") */
            .no-print {
                display: none !important;
            }

            /* Header del modal (título + botón X) */
            .fi-modal-header,
            .fi-modal-close-btn,
            button[aria-label="Close"],
            .fi-icon-btn[aria-label="Close"] {
                display: none !important;
            }

            /* Footer del modal (donde vive el botón Print) */
            .fi-modal-footer,
            .fi-modal-footer-actions {
                display: none !important;
            }

        }






    </style>
@endonce --}}