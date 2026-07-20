<x-filament-panels::page>

    {{-- Tab bar — Pill style --}}
    <div style="display:flex; gap:4px; margin-bottom:1.5rem; flex-wrap:wrap;">

        {{-- Underwritten Overview — oculto temporalmente --}}
        {{-- <button wire:click="$set('activeTab', 'overview')" style="
            display:inline-flex; align-items:center; gap:0.35rem;
            padding:7px 14px; font-size:0.9rem; font-weight:500;
            border:none; cursor:pointer; border-radius:999px; transition:all .15s;
            {{ $activeTab === 'overview'
                ? 'background:rgba(65,162,195,0.15); color:#41A2C3; font-weight:600;'
                : 'background:transparent; color:light-dark(#6b7280,#9ca3af);' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Underwritten Overview
        </button> --}}

        {{-- Portfolio Growth --}}
        <button wire:click="$set('activeTab', 'portfolio')" style="
            display:inline-flex; align-items:center; gap:0.35rem;
            padding:7px 14px; font-size:0.9rem; font-weight:500;
            border:none; cursor:pointer; border-radius:999px; transition:all .15s;
            {{ $activeTab === 'portfolio'
                ? 'background:rgba(65,162,195,0.15); color:#41A2C3; font-weight:600;'
                : 'background:transparent; color:light-dark(#6b7280,#9ca3af);' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                <polyline points="16 7 22 7 22 13"/>
            </svg>
            Portfolio Growth
        </button>

        {{-- Reinsurer Metrics — oculto temporalmente --}}
        {{-- <button wire:click="$set('activeTab', 'analytics')" style="
            display:inline-flex; align-items:center; gap:0.35rem;
            padding:7px 14px; font-size:0.9rem; font-weight:500;
            border:none; cursor:pointer; border-radius:999px; transition:all .15s;
            {{ $activeTab === 'analytics'
                ? 'background:rgba(65,162,195,0.15); color:#41A2C3; font-weight:600;'
                : 'background:transparent; color:light-dark(#6b7280,#9ca3af);' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Reinsurer Metrics
        </button> --}}

        {{-- Portfolio Metrics --}}
        <button wire:click="$set('activeTab', 'budget_plan')" style="
            display:inline-flex; align-items:center; gap:0.35rem;
            padding:7px 14px; font-size:0.9rem; font-weight:500;
            border:none; cursor:pointer; border-radius:999px; transition:all .15s;
            {{ $activeTab === 'budget_plan'
                ? 'background:rgba(65,162,195,0.15); color:#41A2C3; font-weight:600;'
                : 'background:transparent; color:light-dark(#6b7280,#9ca3af);' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Portfolio Metrics
        </button>

        {{-- Reinsurer Segmentation --}}
        <button wire:click="$set('activeTab', 'segmentation')" style="
            display:inline-flex; align-items:center; gap:0.35rem;
            padding:7px 14px; font-size:0.9rem; font-weight:500;
            border:none; cursor:pointer; border-radius:999px; transition:all .15s;
            {{ $activeTab === 'segmentation'
                ? 'background:rgba(65,162,195,0.15); color:#41A2C3; font-weight:600;'
                : 'background:transparent; color:light-dark(#6b7280,#9ca3af);' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
                <path d="M22 12A10 10 0 0 0 12 2v10z"/>
            </svg>
            Reinsurer Segmentation
        </button>

        {{-- GWP Distribution --}}
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

    </div>

    {{-- Tab 1: Underwritten Overview — oculto temporalmente --}}
    {{-- @if ($activeTab === 'overview')
        @livewire(\App\Filament\Underwritten\Widgets\UnderwrittenOverview::class)
        @livewire(\App\Filament\Underwritten\Widgets\UnderwrittenProfile::class)
    @endif --}}

    {{-- Tab 2: Portfolio Growth --}}
    @if ($activeTab === 'portfolio')
        @livewire(\App\Filament\Underwritten\Widgets\PortfolioGrowth::class)
    @endif

    {{-- Tab 3: Reinsurer Metrics — oculto temporalmente --}}
    {{-- @if ($activeTab === 'analytics')
        @livewire(\App\Filament\Underwritten\Widgets\ReinsurerPremiumComparison::class)

        <x-filament::section heading="Monthly Performance">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                @livewire(\App\Filament\Underwritten\Widgets\AnalyticsBusinessChart::class, key('analytics-business'))
                @livewire(\App\Filament\Underwritten\Widgets\AnalyticsPremiumChart::class, key('analytics-premium'))
            </div>
        </x-filament::section>
    @endif --}}

    {{-- Tab 4: Reinsurer Segmentation --}}
    @if ($activeTab === 'segmentation')
        @livewire(\App\Filament\Underwritten\Widgets\ReinsurerSegmentation::class)
    @endif

    {{-- Tab 5: AC vs Plan --}}
    @if ($activeTab === 'budget_plan')
        @livewire(\App\Filament\Underwritten\Widgets\BudgetPlanComparison::class)
    @endif

    {{-- Tab 6: GWP Distribution --}}
    @if ($activeTab === 'gwp_distribution')
        @livewire(\App\Filament\Underwritten\Widgets\GwpPremiumComparison::class)
    @endif

</x-filament-panels::page>
