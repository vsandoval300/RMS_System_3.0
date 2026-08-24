<x-filament-panels::page>

<div x-data="{ helpOpen: false, lang: 'en' }">

    {{-- ── Tab bar ──────────────────────────────────────────────────────────── --}}
    <div style="display:flex; align-items:center; gap:4px; margin-bottom:1.5rem; flex-wrap:wrap;">

        <button wire:click="$set('activeTab', 'portfolio')" style="
            display:inline-flex; align-items:center; gap:0.35rem;
            padding:7px 14px; font-size:0.9rem; font-weight:500;
            border:none; cursor:pointer; border-radius:999px; transition:all .15s;
            {{ $activeTab === 'portfolio'
                ? 'background:rgba(65,162,195,0.15); color:#41A2C3; font-weight:600;'
                : 'background:transparent; color:light-dark(#6b7280,#9ca3af);' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>
            </svg>
            Portfolio Growth
        </button>

        <button wire:click="$set('activeTab', 'budget_plan')" style="
            display:inline-flex; align-items:center; gap:0.35rem;
            padding:7px 14px; font-size:0.9rem; font-weight:500;
            border:none; cursor:pointer; border-radius:999px; transition:all .15s;
            {{ $activeTab === 'budget_plan'
                ? 'background:rgba(65,162,195,0.15); color:#41A2C3; font-weight:600;'
                : 'background:transparent; color:light-dark(#6b7280,#9ca3af);' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Portfolio Metrics
        </button>

        <button wire:click="$set('activeTab', 'segmentation')" style="
            display:inline-flex; align-items:center; gap:0.35rem;
            padding:7px 14px; font-size:0.9rem; font-weight:500;
            border:none; cursor:pointer; border-radius:999px; transition:all .15s;
            {{ $activeTab === 'segmentation'
                ? 'background:rgba(65,162,195,0.15); color:#41A2C3; font-weight:600;'
                : 'background:transparent; color:light-dark(#6b7280,#9ca3af);' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>
            </svg>
            Reinsurer Segmentation
        </button>

        <button wire:click="$set('activeTab', 'gwp_distribution')" style="
            display:inline-flex; align-items:center; gap:0.35rem;
            padding:7px 14px; font-size:0.9rem; font-weight:500;
            border:none; cursor:pointer; border-radius:999px; transition:all .15s;
            {{ $activeTab === 'gwp_distribution'
                ? 'background:rgba(65,162,195,0.15); color:#41A2C3; font-weight:600;'
                : 'background:transparent; color:light-dark(#6b7280,#9ca3af);' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
            </svg>
            Partner Insights
        </button>

        <div style="margin-left:auto;">
            <button @click="helpOpen = true" style="
                display:inline-flex; align-items:center; gap:0.4rem;
                padding:6px 13px; font-size:0.82rem; font-weight:500;
                border:1px solid light-dark(#d1d5db,#374151);
                background:light-dark(#f9fafb,#1f2937);
                color:light-dark(#6b7280,#9ca3af);
                border-radius:999px; cursor:pointer; transition:all .15s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Guide
            </button>
        </div>
    </div>

    {{-- ── Tab content ──────────────────────────────────────────────────────── --}}
    @if ($activeTab === 'portfolio')
        @livewire(\App\Filament\Underwritten\Widgets\PortfolioGrowth::class, ['lazy' => true])
    @endif
    @if ($activeTab === 'segmentation')
        @livewire(\App\Filament\Underwritten\Widgets\ReinsurerSegmentation::class)
    @endif
    @if ($activeTab === 'budget_plan')
        @livewire(\App\Filament\Underwritten\Widgets\BudgetPlanComparison::class)
    @endif
    @if ($activeTab === 'gwp_distribution')
        @livewire(\App\Filament\Underwritten\Widgets\GwpPremiumComparison::class)
    @endif

    {{-- Backdrop --}}
    <div x-show="helpOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="helpOpen = false"
        style="position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:49;"
        x-cloak></div>

    {{-- Slide-over panel — outer div only handles position:fixed + x-show,
         inner div owns the flex layout so Alpine's display toggle doesn't break it --}}
    <div x-show="helpOpen"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="transform translate-x-full" x-transition:enter-end="transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="transform translate-x-0" x-transition:leave-end="transform translate-x-full"
        style="position:fixed; top:0; right:0; bottom:0; width:440px; max-width:92vw; z-index:50;"
        x-cloak>

        <div style="display:flex; flex-direction:column; height:100%;
                    background:light-dark(#ffffff,#1a1a1a);
                    border-left:1px solid light-dark(#e5e7eb,#2d2d2d);
                    box-shadow:-8px 0 32px rgba(0,0,0,0.18);">

        {{-- Panel header --}}
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:16px 20px 12px;
                    border-bottom:1px solid light-dark(#e5e7eb,#2d2d2d);">
            <div>
                <div style="font-size:11px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:light-dark(#9ca3af,#6b7280); margin-bottom:3px;">
                    Underwritten Dashboard
                </div>
                <div style="font-size:15px; font-weight:700; color:light-dark(#111827,#f3f4f6); line-height:1.2;">
                    @if ($activeTab === 'portfolio')        Portfolio Growth
                    @elseif ($activeTab === 'budget_plan')  Portfolio Metrics
                    @elseif ($activeTab === 'segmentation') Reinsurer Segmentation
                    @elseif ($activeTab === 'gwp_distribution') Partner Insights
                    @endif
                    — <span x-text="lang === 'en' ? 'Guide' : 'Guía'"></span>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                {{-- Language toggle --}}
                <div style="display:flex; border:1px solid light-dark(#d1d5db,#374151); border-radius:999px; overflow:hidden;">
                    <button @click="lang = 'en'" style="padding:4px 11px; font-size:11px; font-weight:700; border:none; cursor:pointer; letter-spacing:0.05em; transition:all .15s;"
                        :style="lang === 'en' ? 'background:#41A2C3; color:#ffffff;' : 'background:transparent; color:light-dark(#6b7280,#9ca3af);'">EN</button>
                    <button @click="lang = 'es'" style="padding:4px 11px; font-size:11px; font-weight:700; border:none; cursor:pointer; letter-spacing:0.05em; transition:all .15s;"
                        :style="lang === 'es' ? 'background:#41A2C3; color:#ffffff;' : 'background:transparent; color:light-dark(#6b7280,#9ca3af);'">ES</button>
                </div>
                {{-- Close --}}
                <button @click="helpOpen = false" style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:6px; border:none; cursor:pointer; background:light-dark(#f3f4f6,#2d2d2d); color:light-dark(#6b7280,#9ca3af);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Panel body --}}
        <div style="flex:1; min-height:0; overflow-y:auto; padding:20px;">

            <style>
                .dg-section { font-size:10px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:light-dark(#9ca3af,#6b7280); padding-bottom:6px; border-bottom:1px solid light-dark(#e5e7eb,#2d2d2d); margin-bottom:4px; margin-top:20px; }
                .dg-section:first-child { margin-top:0; }
                .dg-row { padding:11px 0; border-bottom:1px solid light-dark(#f3f4f6,#222); }
                .dg-row:last-child { border-bottom:none; }
                .dg-name { font-weight:600; font-size:12.5px; color:light-dark(#1f2937,#e5e7eb); margin-bottom:4px; }
                .dg-desc { font-size:12.5px; color:light-dark(#4b5563,#9ca3af); line-height:1.55; }
                .dg-kw  { color:light-dark(#1d4ed8,#93c5fd); font-weight:600; }
                .dg-questions { margin-top:8px; padding:8px 10px; border-radius:6px; background:light-dark(#f0f9ff,#0c1a26); border-left:3px solid light-dark(#7dd3fc,#1e4d6b); }
                .dg-q-label { font-size:10px; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; color:light-dark(#0369a1,#38bdf8); margin-bottom:5px; }
                .dg-q { font-size:12px; color:light-dark(#374151,#94a3b8); line-height:1.5; padding:2px 0 2px 14px; position:relative; font-style:italic; }
                .dg-q::before { content:"?"; position:absolute; left:0; font-weight:700; font-style:normal; color:light-dark(#7dd3fc,#38bdf8); font-size:11px; }
            </style>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{-- TAB: Portfolio Growth                                   --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            @if ($activeTab === 'portfolio')

                <div class="dg-section" x-text="lang === 'en' ? 'Filters' : 'Filtros'"></div>

                <div class="dg-row">
                    <div class="dg-name">Reinsurer</div>
                    <div class="dg-desc" x-show="lang === 'en'">Narrows all KPI cards and charts to a single reinsurer. Selecting <em>— All —</em> shows the full portfolio.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Filtra todas las tarjetas KPI y gráficos a una sola reaseguradora. Seleccionar <em>— Todas —</em> muestra el portfolio completo.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How has our portfolio evolved with a specific reinsurer over time?' : '¿Cómo ha evolucionado el portfolio con una reaseguradora específica a lo largo del tiempo?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is growth driven by the whole market or concentrated in a single reinsurer?' : '¿El crecimiento es impulsado por todo el mercado o por una sola reaseguradora?'"></div>
                    </div>
                </div>

                <div class="dg-section" x-text="lang === 'en' ? 'KPI Cards' : 'Tarjetas KPI'"></div>

                <div class="dg-row">
                    <div class="dg-name">Premium CAGR</div>
                    <div class="dg-desc" x-show="lang === 'en'">Compound Annual Growth Rate of gross written premium (USD) from the first recorded year to the prior year. <span class="dg-kw">▲</span> indicates cumulative growth; <span class="dg-kw">▼</span> indicates contraction.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Tasa de Crecimiento Anual Compuesta de la prima bruta suscrita (USD) desde el primer año registrado hasta el año anterior. <span class="dg-kw">▲</span> indica crecimiento acumulado; <span class="dg-kw">▼</span> indica contracción.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'At what rate has our premium portfolio grown since we started operations?' : '¿A qué ritmo ha crecido nuestro portfolio de primas desde que iniciamos operaciones?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Has growth been sustained, or were there periods of significant contraction?' : '¿El crecimiento ha sido sostenido o hubo períodos de contracción significativa?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Cumulative Premium</div>
                    <div class="dg-desc" x-show="lang === 'en'">Total gross underwritten premium accumulated in USD across all recorded years, for the selected reinsurer filter.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Prima bruta suscrita total acumulada en USD a lo largo de todos los años registrados, para el filtro de reaseguradora seleccionado.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How much have we accumulated in gross premiums since the business began?' : '¿Cuánto hemos acumulado en primas brutas desde que inició el negocio?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'What is the total historical size of our underwriting portfolio?' : '¿Cuál es el tamaño total histórico de nuestro portfolio?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Record Year</div>
                    <div class="dg-desc" x-show="lang === 'en'">The year with the highest gross written premium and business count. The <span class="dg-kw">Peak</span> badge marks the historical maximum.</div>
                    <div class="dg-desc" x-show="lang === 'es'">El año con la mayor prima bruta suscrita y número de negocios. La insignia <span class="dg-kw">Peak</span> marca el máximo histórico.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which was our best year in terms of production?' : '¿Cuál fue nuestro mejor año en términos de producción?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Have we already surpassed our all-time record, or does it still stand as the peak?' : '¿Hemos superado ya nuestro récord histórico, o sigue siendo el pico máximo?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Active Reinsurers</div>
                    <div class="dg-desc" x-show="lang === 'en'">Number of distinct reinsurers with at least one business in the current year. Delta shows change vs. prior year.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Número de reaseguradoras distintas con al menos un negocio en el año actual. El delta muestra el cambio vs. el año anterior.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Are we expanding or concentrating our reinsurer base?' : '¿Estamos ampliando o concentrando nuestra base de reaseguradoras?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Did we gain or lose active reinsurers compared to the prior year?' : '¿Ganamos o perdimos reaseguradoras activas respecto al año anterior?'"></div>
                    </div>
                </div>

                <div class="dg-section" x-text="lang === 'en' ? 'Charts' : 'Gráficos'"></div>

                <div class="dg-row">
                    <div class="dg-name">Businesses per Year</div>
                    <div class="dg-desc" x-show="lang === 'en'">Annual count of underwritten businesses. <span class="dg-kw" style="color:#2563eb;">Blue</span> baseline = first recorded year. <span class="dg-kw" style="color:#16a34a;">Green</span> = YoY growth; <span class="dg-kw" style="color:#dc2626;">red</span> = YoY decline.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Conteo anual de negocios suscritos. La línea base <span class="dg-kw" style="color:#2563eb;">azul</span> marca el primer año registrado. <span class="dg-kw" style="color:#16a34a;">Verde</span> = crecimiento interanual; <span class="dg-kw" style="color:#dc2626;">rojo</span> = caída interanual.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How much did the number of businesses grow in a given year vs. the previous one?' : '¿Cuánto creció el número de negocios de un año respecto del anterior?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which year has seen the highest growth rate since the business started?' : '¿Cuál ha sido el año con mayor crecimiento desde que inició el negocio?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'In which year did we record the largest drop, and what might have caused it?' : '¿En qué año registramos la mayor caída y a qué puede atribuirse?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is growth consistent over time, or were there particularly irregular years?' : '¿La tendencia de crecimiento es consistente o hubo años irregulares?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Underwritten Premium</div>
                    <div class="dg-desc" x-show="lang === 'en'">Annual gross written premium in USD (millions). Same color convention as the businesses chart.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Prima bruta suscrita anual en USD (millones). Misma convención de colores que el gráfico de negocios.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'In which year was the highest premium volume generated?' : '¿En qué año se generó el mayor volumen de primas?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Does premium growth follow the same trend as business count, or is there a divergence?' : '¿El crecimiento en primas sigue la misma tendencia que el número de negocios, o hay divergencia?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How much did premiums grow this year vs. the prior year?' : '¿Cuánto crecieron las primas este año vs. el año anterior?'"></div>
                    </div>
                </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{-- TAB: Portfolio Metrics                                  --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            @elseif ($activeTab === 'budget_plan')

                <div class="dg-section" x-text="lang === 'en' ? 'Filters' : 'Filtros'"></div>

                <div class="dg-row">
                    <div class="dg-name">Year</div>
                    <div class="dg-desc" x-show="lang === 'en'">Reference year for all metrics. Actual figures (AC) are compared against the prior year (PY) and, optionally, the budget plan (PL).</div>
                    <div class="dg-desc" x-show="lang === 'es'">Año de referencia para todas las métricas. Las cifras reales (AC) se comparan contra el año anterior (PY) y, opcionalmente, contra el plan presupuestado (PL).</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How do the results of a specific year compare against the prior year?' : '¿Cómo se comparan los resultados de un año específico contra el anterior?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Reinsurer</div>
                    <div class="dg-desc" x-show="lang === 'en'">Limits all KPIs and charts to a single reinsurer. Defaults to the full portfolio.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Limita todos los KPIs y gráficos a una sola reaseguradora. Por defecto muestra el portfolio completo.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How did a specific reinsurer perform in the selected year vs. its own prior year?' : '¿Cómo se desempeñó una reaseguradora específica en el año seleccionado vs. su propio año anterior?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Show Plan / Budget</div>
                    <div class="dg-desc" x-show="lang === 'en'">Overlays the selected budget scenario onto all metrics, adding <span class="dg-kw">ΔPL</span> columns that compare actuals vs. the plan target.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Superpone el escenario presupuestario seleccionado sobre todas las métricas, agregando columnas <span class="dg-kw">ΔPL</span> que comparan los reales vs. el objetivo del plan.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Are we on track to meet the budget we set for this year?' : '¿Estamos alcanzando el presupuesto que nos fijamos para este año?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How far are we from the plan in terms of premium and business count?' : '¿Qué tan lejos estamos del plan en primas y en número de negocios?'"></div>
                    </div>
                </div>

                <div class="dg-section" x-text="lang === 'en' ? 'KPI Cards' : 'Tarjetas KPI'"></div>

                <div class="dg-row">
                    <div class="dg-name">Gross Premium (USD)</div>
                    <div class="dg-desc" x-show="lang === 'en'">Total actual gross written premium (AC) for the selected year. <span class="dg-kw">ΔPY</span> = change vs. prior year. <span class="dg-kw">ΔPL</span> = deviation from plan (when Show Plan is active).</div>
                    <div class="dg-desc" x-show="lang === 'es'">Prima bruta suscrita real total (AC) para el año seleccionado. <span class="dg-kw">ΔPY</span> = cambio vs. año anterior. <span class="dg-kw">ΔPL</span> = desviación respecto al plan (cuando Show Plan está activo).</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How much did we generate in gross premiums in the selected year?' : '¿Cuánto generamos en primas brutas en el año seleccionado?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Are we above or below the prior year?' : '¿Estamos por encima o por debajo del año anterior?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How close are we to reaching the budgeted plan?' : '¿Qué tan cerca estamos de alcanzar el plan presupuestado?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Businesses</div>
                    <div class="dg-desc" x-show="lang === 'en'">Count of businesses underwritten in the selected year. <span class="dg-kw">ΔPY</span> shows the change vs. the prior year.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Número de negocios suscritos en el año seleccionado. <span class="dg-kw">ΔPY</span> muestra el cambio vs. el año anterior.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Did we place more or fewer businesses than the prior year?' : '¿Colocamos más o menos negocios que el año anterior?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is premium growth accompanied by matching growth in business volume?' : '¿El crecimiento en prima va acompañado de un crecimiento en volumen de negocios?'"></div>
                    </div>
                </div>

                <div class="dg-section" x-text="lang === 'en' ? 'Charts' : 'Gráficos'"></div>

                <div class="dg-row">
                    <div class="dg-name">Gross Premium by Reinsurer</div>
                    <div class="dg-desc" x-show="lang === 'en'">Breakdown of the selected year's premium by reinsurer. Toggle between <em>Table</em> (with <span class="dg-kw">ΔPY</span> and <span class="dg-kw">ΔPL%</span>) and <em>Bar chart</em> views. <span class="dg-kw">% AC</span> = each reinsurer's share of the total portfolio.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Desglose de la prima del año seleccionado por reaseguradora. Alterna entre vista de <em>Tabla</em> (con <span class="dg-kw">ΔPY</span> y <span class="dg-kw">ΔPL%</span>) y <em>Gráfico de barras</em>. <span class="dg-kw">% AC</span> = participación de cada reaseguradora en el total del portfolio.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which reinsurer holds the largest share of our portfolio?' : '¿Qué reaseguradora concentra la mayor proporción de nuestro portfolio?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Do we have a risky concentration in a single reinsurer?' : '¿Tenemos una concentración riesgosa en una sola reaseguradora?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which reinsurers are growing and which are losing share vs. the prior year?' : '¿Qué reaseguradoras están creciendo y cuáles perdiendo participación vs. el año anterior?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Monthly Performance</div>
                    <div class="dg-desc" x-show="lang === 'en'">Month-by-month premium comparison: <span class="dg-kw">AC</span> (actual current year) vs. <span class="dg-kw">PY</span> (prior year), and optionally vs. <span class="dg-kw">Plan</span>.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Comparación mensual de prima: <span class="dg-kw">AC</span> (año actual real) vs. <span class="dg-kw">PY</span> (año anterior), y opcionalmente vs. <span class="dg-kw">Plan</span>.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'In which months of the year is production most concentrated?' : '¿En qué meses del año se concentra la mayor producción?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Are we tracking in line with last year\'s pace year-to-date?' : '¿Estamos en línea con el ritmo del año anterior en lo que va del año?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is there a clear seasonal pattern in our business?' : '¿Hay estacionalidad marcada en nuestro negocio?'"></div>
                    </div>
                </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{-- TAB: Reinsurer Segmentation                             --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            @elseif ($activeTab === 'segmentation')

                <div class="dg-section" x-text="lang === 'en' ? 'Filters' : 'Filtros'"></div>

                <div class="dg-row">
                    <div class="dg-name">Year</div>
                    <div class="dg-desc" x-show="lang === 'en'">Selects the year for which clustering runs. Changing the year recalculates all clusters and the dendrogram.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Selecciona el año para el cual se ejecuta el agrupamiento. Cambiar el año recalcula todos los grupos y el dendrograma.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How has the reinsurer segmentation changed from one year to the next?' : '¿Cómo ha cambiado la segmentación de reaseguradoras de un año a otro?'"></div>
                    </div>
                </div>

                <div class="dg-section" x-text="lang === 'en' ? 'Cluster Summary Cards' : 'Tarjetas de Resumen por Grupo'"></div>

                <div class="dg-row">
                    <div class="dg-name" x-text="lang === 'en' ? 'Cluster cards' : 'Tarjetas de grupo'"></div>
                    <div class="dg-desc" x-show="lang === 'en'">Each card represents a group of reinsurers with similar premium volume. Shows reinsurer count and combined gross premium (USD). Colors match the leaf labels in the dendrogram below.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Cada tarjeta representa un grupo de reaseguradoras con volumen de prima similar. Muestra el número de reaseguradoras y la prima bruta combinada (USD). Los colores coinciden con las etiquetas del dendrograma.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How many distinct groups of reinsurers exist based on premium volume?' : '¿Cuántos grupos diferenciados de reaseguradoras existen según su volumen de prima?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'What share of the total portfolio does the largest group hold?' : '¿Qué proporción del portfolio total concentra el grupo más grande?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is there a group of smaller reinsurers that together represent a significant volume?' : '¿Hay un grupo de reaseguradoras más pequeñas que en conjunto representan un volumen significativo?'"></div>
                    </div>
                </div>

                <div class="dg-section" x-text="lang === 'en' ? 'Dendrogram' : 'Dendrograma'"></div>

                <div class="dg-row">
                    <div class="dg-name" x-text="lang === 'en' ? 'Reinsurer Segmentation (chart)' : 'Segmentación de Reaseguradoras (gráfico)'"></div>
                    <div class="dg-desc" x-show="lang === 'en'">Hierarchical tree diagram grouping reinsurers by premium similarity. Bar height = gross premium (USD). Reinsurers that merge at a lower height are more similar. Leaf labels are colored by cluster assignment.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Diagrama de árbol jerárquico que agrupa reaseguradoras por similitud de prima. La altura de cada barra = prima bruta (USD). Las reaseguradoras que se fusionan a menor altura son más similares entre sí. Las etiquetas de las hojas están coloreadas según el grupo asignado.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which reinsurers are most similar to each other in terms of premium volume?' : '¿Qué reaseguradoras son más similares entre sí en términos de volumen de prima?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is there a reinsurer so dominant that it operates in its own category?' : '¿Existe alguna reaseguradora tan dominante que opera en una categoría propia?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which reinsurers would make sense to group together in a joint negotiation or strategy?' : '¿Con qué reaseguradoras tendría sentido agrupar una negociación o estrategia conjunta?'"></div>
                    </div>
                </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{-- TAB: Partner Insights                                   --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            @elseif ($activeTab === 'gwp_distribution')

                <div class="dg-section" x-text="lang === 'en' ? 'Filters' : 'Filtros'"></div>

                <div class="dg-row">
                    <div class="dg-name">Year</div>
                    <div class="dg-desc" x-show="lang === 'en'">Reference year for all KPIs and charts. Prior year (PY) figures are calculated automatically from the year before.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Año de referencia para todos los KPIs y gráficos. Las cifras del año anterior (PY) se calculan automáticamente a partir del año previo.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How does this year\'s fee and premium structure compare to the prior year?' : '¿Cómo se compara la estructura de comisiones y primas de este año vs. el anterior?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Reinsurer / Retrocedant / Cedant</div>
                    <div class="dg-desc" x-show="lang === 'en'">Cross-filters that narrow all KPIs and charts simultaneously to the selected partner combination.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Filtros cruzados que reducen simultáneamente todos los KPIs y gráficos a la combinación de socios seleccionada.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'What is the cost and commission structure for a specific partner or combination of partners?' : '¿Cuál es la estructura de costos y comisiones de un socio o combinación de socios específica?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is a particular cedant\'s net margin better or worse than the portfolio average?' : '¿El margen neto de un cedante particular es mejor o peor que el promedio del portfolio?'"></div>
                    </div>
                </div>

                <div class="dg-section" x-text="lang === 'en' ? 'KPI Cards' : 'Tarjetas KPI'"></div>

                <div class="dg-row">
                    <div class="dg-name">Gross Premium (USD)</div>
                    <div class="dg-desc" x-show="lang === 'en'">Total gross written premium for the selected year and active filters. <span class="dg-kw">ΔPY</span> shows the change vs. the prior year.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Prima bruta total suscrita para el año seleccionado y los filtros activos. <span class="dg-kw">ΔPY</span> muestra el cambio vs. el año anterior.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How much did we generate in gross premiums with this group of partners in the selected year?' : '¿Cuánto generamos en prima bruta con este grupo de socios en el año seleccionado?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Did we grow or shrink vs. the prior year in this segment?' : '¿Crecimos o decrecimos vs. el año anterior en este segmento?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Cedant / Retrocedant / Referral / Management Fee</div>
                    <div class="dg-desc" x-show="lang === 'en'">Each fee card shows the total commission or charge as a percentage of gross premium for that partner type.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Cada tarjeta de comisión muestra el total de comisión o cargo como porcentaje de la prima bruta para ese tipo de socio.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'What proportion of gross premium is allocated to each type of commission or fee?' : '¿Qué proporción de la prima bruta se destina a cada tipo de comisión o cargo?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is the commission mix within expected parameters, or is any item out of range?' : '¿El mix de comisiones está dentro de los parámetros esperados o hay alguna partida fuera de rango?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Net Underwritten Premium</div>
                    <div class="dg-desc" x-show="lang === 'en'">Gross premium after deducting all fees and commissions. Expressed as both an absolute USD amount and a percentage of gross premium.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Prima bruta después de deducir todas las comisiones y cargos. Se expresa tanto en monto absoluto (USD) como en porcentaje de la prima bruta.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'How much do we retain after all deductions?' : '¿Cuánto nos queda realmente después de todas las deducciones?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is the net margin improving or deteriorating vs. the prior year?' : '¿El margen neto está mejorando o deteriorándose vs. el año anterior?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which partner combination generates the best net margin?' : '¿Qué combinación de socios genera el mejor margen neto?'"></div>
                    </div>
                </div>

                <div class="dg-section" x-text="lang === 'en' ? 'Charts' : 'Gráficos'"></div>

                <div class="dg-row">
                    <div class="dg-name">Gross Premium by Reinsurer</div>
                    <div class="dg-desc" x-show="lang === 'en'">Horizontal bar chart ranking reinsurers by gross premium share (AC vs. PY). <span class="dg-kw">% GWP</span> = each reinsurer's portfolio share. Average reference line marks the mean contribution.</div>
                    <div class="dg-desc" x-show="lang === 'es'">Gráfico de barras horizontal que clasifica las reaseguradoras por su participación en prima bruta (AC vs. PY). <span class="dg-kw">% GWP</span> = participación de cada reaseguradora en el portfolio. La línea de referencia promedio marca la contribución media.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which reinsurer contributes the largest share of gross premium?' : '¿Qué reaseguradora aporta la mayor proporción de primas brutas?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Is reinsurer concentration increasing or becoming more evenly distributed?' : '¿La concentración por reaseguradora está aumentando o distribuyéndose más uniformemente?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Gross Premium by Retrocedant</div>
                    <div class="dg-desc" x-show="lang === 'en'">Same breakdown grouped by retrocedant. Useful for evaluating channel concentration and performance.</div>
                    <div class="dg-desc" x-show="lang === 'es'">El mismo desglose agrupado por retrocedente. Útil para evaluar la concentración y el desempeño por canal de distribución.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which retrocession channel generates the highest premium volume?' : '¿Qué canal de retrocesión genera el mayor volumen de prima?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Are we diversifying our channels or depending heavily on a single one?' : '¿Estamos diversificando nuestros canales o dependemos de uno solo?'"></div>
                    </div>
                </div>

                <div class="dg-row">
                    <div class="dg-name">Gross Premium by Cedant Company</div>
                    <div class="dg-desc" x-show="lang === 'en'">Same breakdown grouped by the originating cedant company.</div>
                    <div class="dg-desc" x-show="lang === 'es'">El mismo desglose agrupado por la compañía cedente originaria.</div>
                    <div class="dg-questions">
                        <div class="dg-q-label" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which cedant carries the greatest weight in our portfolio?' : '¿Qué cedante tiene el mayor peso en nuestro portfolio?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Do we have a client concentration that represents a portfolio risk?' : '¿Tenemos una concentración de clientes que represente un riesgo de cartera?'"></div>
                        <div class="dg-q" x-text="lang === 'en' ? 'Which cedants have grown or reduced their share vs. the prior year?' : '¿Qué cedantes han crecido o reducido su participación vs. el año anterior?'"></div>
                    </div>
                </div>

            @endif

        </div>{{-- /panel body --}}
        </div>{{-- /flex layout --}}
    </div>{{-- /panel --}}

</div>{{-- /x-data --}}

</x-filament-panels::page>
