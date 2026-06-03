<div
    class="border border-gray-800 rounded p-4 space-y-4"
    style="
        max-height:1200px;
        background-color:#f1efea;
        color:#1f262a;
        font-family:'Montserrat',sans-serif;
        /* 👇 clave: el reporte usa el alto del viewport */
        max-height: calc(100vh - 220px);
        overflow: auto;
    "
>
    <div class="min-w-[1200px]" style="padding: 24px;">

        {{-- MAIN TITTLE --}}

        <h4 class="font-semibold mt-6 mb-4" style="color:#db4a2b; font-size:15px; font-weight: 600;">
            <span class="py-1 text-left font-extrabold text-gray-300 w-1/4"></span>
            <span class="py-1 w-1/4">{{ $id ?? '-' }}</span>
        </h4>
        
        {{--CURRENT DATE 
        <div class="text-right text-sm font-medium text-gray-600 mb-2" style="border-bottom: 1px solid #100f0d;">
            Date: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div> --}}
        <br>
        {{-------------------------------------------------------------------------------}}
        {{-- DOCUMENT DETAILS SECTION                                                  --}}
        {{-------------------------------------------------------------------------------}}
        <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px; font-weight: 600;">
            General Details
        </h4>
        <br>
        
        <table class="w-full table-auto text-sm border-separate border-spacing-0">
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
                <tr>
                    <td class="px-2 py-1 text-left font-semibold" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                        Document type:
                    </td>
                    <td class="px-2 py-1 font-thin border-b" style="border-bottom: 1px solid #100f0d;">{{ $documentType ?? '-' }}</td>
                    <td class="px-2 py-1 text-left font-semibold border-b" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                        Creation date:
                    </td>
                    <td class="px-2 py-1 border-b" style="border-bottom: 1px solid #100f0d;">
                        {{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d/m/Y') : '-' }}
                    </td>
                    {{-- columnas “dummy” --}}
                    <td></td>
                    <td></td>
                </tr>

                <tr>
                    <td class="px-2 py-1 text-left font-semibold border-b " style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                        Premium type:
                    </td>
                    <td class="px-2 py-1 font-thin border-b" style="border-bottom: 1px solid #100f0d;">{{ $premiumType ?? '-' }}</td>
                    <td class="px-2 py-1 text-left font-semibold border-b" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                        Period:
                    </td>
                    <td class="px-2 py-1 font-thin border-b" style="border-bottom: 1px solid #100f0d;">
                        {{ $inceptionDate ? \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') : '-' }}
                        to
                        {{ $expirationDate ? \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') : '-' }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>

                <tr>
                    <td class="px-2 py-1 text-left font-semibold border-b" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                        Original currency:
                    </td>
                    <td class="px-2 py-1 font-thin border-b" style="border-bottom: 1px solid #100f0d;">{{ $originalCurrency ?? '-' }}</td>
                    <td class="px-2 py-1 text-left font-semibold border-b" style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                        Coverage days:
                    </td>
                    <td class="px-2 py-1 font-thin border-b" style="border-bottom: 1px solid #100f0d;">
                        {{ isset($coverageDays) ? (int) $coverageDays : '-' }}
                    </td>
                    
                    <td></td>
                    <td></td>
                </tr>

                {{-- ✅✅✅ CHANGE [ROE-VIEW-1]: Nueva fila para mostrar RoE for Reference --}}
                <tr>
                    <td class="px-2 py-1 text-left font-semibold border-b " style="color: #100f0d; border-bottom: 1px solid #100f0d; font-weight: 600;">
                        RoE for Reference:
                    </td>
                    <td class="px-2 py-1 font-thin border-b" style="border-bottom: 1px solid #100f0d;">
                        {{ isset($roe_fs) ? number_format((float) $roe_fs, 8) : '-' }}
                    </td>

                    {{-- columnas vacías para mantener la estructura 2x2 --}}
                    <td style="border-bottom: 1px solid #100f0d;"></td>
                    <td style="border-bottom: 1px solid #100f0d;"></td>
                    <td></td>
                    <td></td>
                </tr>


            </tbody>
        </table>

        <br>

        {{-------------------------------------------------------------------------------}}
        {{-- PLACEMENT SCHEMES                                                         --}}
        {{-------------------------------------------------------------------------------}}
        <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px; font-weight: 600;">
        Placement Schemes
        </h4>

        <table class="w-full table-fixed border-collapse text-sm">
            <colgroup>
                <col style="width:5%;">
                <col style="width:20%;">
                <col style="width:45%;">
                <col style="width:20%;">
                <col style="width:20%;">
            </colgroup>
            <thead>
                <tr class="border-b text-gray-300 border-gray-600">
                    <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d; text-align:left";>#</th>
                    <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d; text-align:left";">Id</th>
                    <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d; text-align:left";">Description</th>
                    <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d; text-align:left";">Share (%)</th>
                    <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d; text-align:left";">Agreement Type</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($costSchemes ?? [] as $index => $scheme)
                    <tr class="bg-gray-800 text-gray-300 border-b border-gray-600">

                        <td class="px-2 py-1" style="padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">{{ $index + 1 }}</td>
                        <td class="px-2 py-1" style= "padding-left: 0.5rem; padding-right: 0.5rem;border-bottom: 1px solid #100f0d;">{{ $scheme['id'] ?? '-' }}</td>

                        <td class="px-2 py-1" style="padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;"> {{-- ✅ MOD [PS-DESC-3] NEW --}}
                            {{ $scheme['description'] ?? '-' }}
                        </td>

                        <td class="px-2 py-1 text-center" style="padding-left: 0.5rem; padding-right: 0.5rem;border-bottom: 1px solid #100f0d;">
                            {{ isset($scheme['share']) ? number_format($scheme['share'] * 100, 2) . '%' : '-' }}
                        </td>
                        <td class="px-2 py-1 text-center" style="padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">{{ $scheme['agreement_type'] ?? '-' }}</td>
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



        <br>
        {{-------------------------------------------------------------------------------}}
        {{-- INSUREDS (grouped by Placement Scheme)                                      --}}
        {{-------------------------------------------------------------------------------}}

        <h4 class="font-semibold mt-6 mb-4" style="color: #db4a2b; font-size: 15px; font-weight: 600;">
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
                        Placement Scheme: <span class="font-bold" style="font-weight: 700;">{{ $schemeLabel }}</span>
                    </div>

                    <table
                        class="w-full text-sm border-separate border-spacing-y-1 mt-2 table-fixed"  {{-- ✅ NEW: table-fixed --}}
                        style="table-layout: fixed; width: 100%;" {{-- ✅ NEW: forzado --}}
                    >
                        <colgroup>
                            <col style="width:4%;">   {{-- # --}}
                            <col style="width:20%;">  {{-- Insured --}}
                            <col style="width:20%;">  {{-- Coverage --}}
                            <col style="width:8%;">   {{-- Share --}}
                            <col style="width:8%;">   {{-- Country --}}
                            <col style="width:9%;">   {{-- Allocation --}}
                            <col style="width:9%;">   {{-- Annual Premium --}}
                            <col style="width:9%;">   {{-- Annual Premium Ftp --}}
                            <col style="width:9%;">   {{-- Annual Premium Fts --}}
                        </colgroup>

                        <thead>
                            <tr class="border-b border-gray-600">
                                <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d; text-align:left;">#</th>
                                <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d; text-align:left;">Insured</th>
                                <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d; text-align:left;">Coverage</th>
                                <th class="px-2 py-1 text-right font-semibold" style="color: #100f0d; text-align:left;">Share</th>
                                <th class="px-2 py-1 text-left font-semibold" style="color: #100f0d; text-align:left;">Country</th>
                                <th class="px-2 py-1 text-right font-semibold" style="color: #100f0d; text-align:left;">Allocation</th>
                                <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d; text-align:center;">Annual<br>Premium</th>
                                <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d; text-align:center;">Annual<br>Premium Ftp</th>
                                <th class="px-2 py-1 text-center font-semibold" style="color: #100f0d; text-align:center;">Annual<br>Premium Fts</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($rows->values() as $index => $insured)
                                <tr class="bg-gray-800 text-gray-300 border-b border-gray-600">
                                    <td class="px-2 py-1" style="border-bottom: 1px solid #100f0d;">{{ $index + 1 }}</td>

                                    <td class="px-2 py-1 truncate" style="border-bottom: 1px solid #100f0d;" title="{{ $insured['company']['name'] ?? '-' }}">
                                        {{ $insured['company']['name'] ?? '-' }}
                                    </td>

                                    <td class="px-2 py-1 truncate" style="border-bottom: 1px solid #100f0d;" title="{{ $insured['coverage']['name'] ?? '-' }}">
                                        {{ $insured['coverage']['name'] ?? '-' }}
                                    </td>

                                    <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d;">
                                        {{ number_format($schemeShare * 100, 2) . '%' }}
                                    </td>

                                    <td class="px-2 py-1 truncate" style="border-bottom: 1px solid #100f0d;" title="{{ $insured['company']['country']['name'] ?? '-' }}">
                                        {{ $insured['company']['country']['name'] ?? '-' }}
                                    </td>

                                    <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d;">
                                        {{ isset($insured['allocation_percent']) ? number_format($insured['allocation_percent'] * 100, 2) . '%' : '-' }}
                                    </td>

                                    <td class="px-2 py-1 text-right whitespace-nowrap" style="border-bottom: 1px solid #100f0d; text-align:right;">
                                        ${{ number_format($insured['premium'] ?? 0, 2) }}
                                    </td>

                                    <td class="px-2 py-1 text-right whitespace-nowrap" style="border-bottom: 1px solid #100f0d; text-align:right;">
                                        ${{ number_format($insured['premium_ftp'] ?? 0, 2) }}
                                    </td>

                                    <td class="px-2 py-1 text-right whitespace-nowrap" style="border-bottom: 1px solid #100f0d; text-align:right;">
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

                                <td class="px-2 py-1 text-right font-semibold" style="color:#100f0d; font-weight: 600; text-align:right;">Totals:</td>
                                <td class="px-2 py-1 text-right font-semibold" style="color:#100f0d; font-weight: 600; text-align:right;">
                                    {{ number_format($totalAllocation * 100, 2) . '%' }}
                                </td>
                                <td class="px-2 py-1 text-right font-semibold whitespace-nowrap" style="color:#100f0d; font-weight: 600; text-align:right;">
                                    ${{ number_format($totalPremium, 2) }}
                                </td>
                                <td class="px-2 py-1 text-right font-semibold whitespace-nowrap" style="color:#100f0d; font-weight: 600; text-align:right;">
                                    ${{ number_format($totalFtp, 2) }}
                                </td>
                                <td class="px-2 py-1 text-right font-semibold whitespace-nowrap" style="color:#100f0d; font-weight: 600; text-align:right;">
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


        
        <table
            class="w-full text-sm border-separate border-spacing-y-1 mt-2 table-fixed"
            style="table-layout: fixed; width: 100%; border-collapse: collapse;" {{-- ✅ NEW: forzado --}}
        >
            <colgroup>
                <col style="width:4%;">   {{-- # --}}
                <col style="width:60%;">  {{-- Insured --}}
                <col style="width:10%;">  {{-- Coverage --}}
                <col style="width:8%;">   {{-- Share --}}
                <col style="width:8%;">   {{-- Country --}}
                <col style="width:10%;">   {{-- Allocation --}}
                <col style="width:10%;">   {{-- Annual Premium --}}
            </colgroup>
            <thead>
                
                <tr>
                    <th class="px-2 py-1 text-gray-400"></th>
                    <th class="px-2 py-1 text-gray-400"></th>
                    <th class="px-2 py-1 text-gray-400"></th>
                    <th class="px-2 py-1 text-gray-400"></th> 
                    <th class="px-2 py-1  text-gray-400"></th>
                    <th class="px-2 py-1" style="color: #100f0d; font-weight: 600; padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">Orig. Curr.</th>
                    <th class="px-2 py-1" style="color: #100f0d; font-weight: 600; padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">US Dollars</th>
                </tr>

            </thead>

            <tbody>

                <tr class="bg-gray-900 text-gray-300 font-semibold">
                    <td colspan="5" class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">Gross Underwritten Premium</td>
                    <td class="px-2 py-1 text-right border-t border-gray-600 font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">${{ number_format($totalPremiumFts ?? 0, 2) }}</td>
                    <td class="px-2 py-1 text-right border-t border-gray-600 font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">${{ number_format($totalConvertedPremium ?? 0, 2) }}</td>
                </tr>

                <tr><td colspan="7" class="py-2"></td></tr>


                {{-- Table headers for each group --}}
                    <tr class="text-sm text-gray-300 uppercase">
                        <th class="px-2 py-1 font-semibold" style="color: #100f0d; border-bottom: 1px solid #100f0d; text-align:left";">#</th>
                        <th class="px-2 py-1 font-semibold" style="color: #100f0d; border-bottom: 1px solid #100f0d; text-align:left";">Partner</th>
                        <th class="px-2 py-1" style="color: #100f0d; border-bottom: 1px solid #100f0d; text-align:left";">Share</th> 
                        <th class="px-2 py-1 font-semibold" style="color: #100f0d; border-bottom: 1px solid #100f0d; text-align:left";">Concept</th>
                        <th class="px-2 py-1  font-semibold" style="color: #100f0d; border-bottom: 1px solid #100f0d; text-align:left";">Value</th>
                        <th class="px-2 py-1" style="color: #100f0d; border-bottom: 1px solid #100f0d;"></th>
                        <th class="px-2 py-1" style="color: #100f0d; border-bottom: 1px solid #100f0d;"></th>
                    <th class="px-2 py-1 text-right"></th>
                    </tr>





                @forelse ($groupedCostNodes ?? [] as $group)

                    {{-- <tr>
                        <td colspan="7">
                            <div class="border-t border-gray-600 my-2"></div>
                        </td>
                    </tr>

                    Table headers for each group 
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
                        <td colspan="7" class="px-0 py-1" style="padding-left: 0px; padding-right: 0px; padding-top: 0.25rem; padding-bottom: 0.25rem;">
                            <div class="border-t border-gray-600 w-full h-px" style="height: 1px; width: 100%;"></div>
                        </td>
                    </tr>

                    {{-- Deduction detail rows --}}
                    @foreach ($group['nodes'] as $node)
                        <tr class="bg-gray-800 text-gray-300 border-b border-gray-600">
                            <td class="px-2 py-1" style="padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">{{ $node['index'] }}</td>
                            <td class="px-2 py-1" style="padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">{{ $node['partner'] ?? '-' }}</td>                    <!-- VARIABLE NUEVA -->
                            <td class="px-2 py-1" style="padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">{{ number_format($node['share'] * 100, 2) }}%</td>
                            <td class="px-2 py-1" style="padding-left: 0.5rem; padding-right: 0.5rem; border-bottom: 1px solid #100f0d;">{{ $node['deduction'] ?? '-' }}</td>
                            <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d; text-align:right;">{{ number_format($node['value'] * 100, 2) }}%</td>
                            <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d; text-align:right;">${{ number_format($node['deduction_amount']* -1, 2) }}</td>
                            <td class="px-2 py-1 text-right" style="border-bottom: 1px solid #100f0d; text-align:right;">${{ number_format($node['deduction_usd']* -1, 2) }}</td>
                        </tr>
                    @endforeach

                    {{-- Subtotal row BELOW each group --}}
                    <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                        <td colspan="4" class="px-2 py-1 text-left font-semibold" style="color: #100f0d; font-weight: 600;">
                            Share {{ number_format($group['share'] * 100, 2) }}%.
                        </td> 
                        <td class="px-2 py-1 text-right text-gray-300 font-semibold" style="color: #100f0d; font-weight: 600; text-align:right;">Subtotal:</td>
                        <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align:right;">${{ number_format($group['subtotal_orig']* -1, 2) }}</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align:right;">${{ number_format($group['subtotal_usd']* -1, 2) }}</td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="3" class="px-2 py-2 text-center text-gray-400 font-semibold" style="color: #100f0d; font-weight: 600;">No cost nodes available</td>
                    </tr>
                @endforelse


                {{-- Grand total --}}
                @php
                    $grandTotalOrig = collect($groupedCostNodes ?? [])->sum('subtotal_orig');
                    $grandTotalUsd = collect($groupedCostNodes ?? [])->sum('subtotal_usd');
                @endphp
                <tr class="border-t border-gray-600 bg-gray-900 text-gray-300 font-semibold">
                    <td colspan="5" class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">Total Deductions:</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem;">${{ number_format($grandTotalOrig * -1, 2) }}</td>
                    <td class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem;">${{ number_format($grandTotalUsd * -1, 2) }}</td>
                </tr>
                <tr><td colspan="4" class="py-2" style="padding-top: 0.5rem; padding-bottom: 0.5rem;"></td></tr>
                <tr class="bg-gray-900 text-gray-300 font-semibold">
                    <td colspan="5" class="px-2 py-1 text-right font-semibold" style="color: #100f0d; font-weight: 600;text-align: right; padding-left: 0.5rem; padding-right: 0.5rem;">Net Underwritten Premium</td>

                    <td class="px-2 py-1 text-right border-t border-gray-600" style="border-top: 1px solid #100f0d; color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem;">
                        ${{ number_format($netUnderwrittenOrig ?? 0, 2) }}
                    </td>
                    <td class="px-2 py-1 text-right border-t border-gray-600" style="border-top: 1px solid #100f0d; color: #100f0d; font-weight: 600; text-align: right; padding-left: 0.5rem;">
                        ${{ number_format($netUnderwrittenUsd ?? 0, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>




        <div style="height: 32px;"></div>




    </div>
</div>
