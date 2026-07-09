<x-filament-panels::page>

    {{-- Tab bar --}}
    <div style="
        display: flex;
        gap: 0;
        border-bottom: 1px solid light-dark(rgba(0,0,0,0.1), rgba(255,255,255,0.1));
        margin-bottom: 1.5rem;
    ">
        {{-- Underwritten Overview — oculto temporalmente --}}
        {{-- <button
            wire:click="$set('activeTab', 'overview')"
            style="
                padding: 10px 20px; font-size: 1rem; font-weight: 500;
                border: none; background: transparent; cursor: pointer; transition: all .15s;
                display: inline-flex; align-items: center; gap: 0.4rem;
                {{ $activeTab === 'overview'
                    ? 'color:#41A2C3; border-bottom:2px solid #41A2C3; margin-bottom:-1px;'
                    : 'color: light-dark(#6b7280,#9ca3af); border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Underwritten Overview
        </button> --}}

        {{-- Portfolio Growth --}}
        <button
            wire:click="$set('activeTab', 'portfolio')"
            style="
                padding: 10px 20px; font-size: 1rem; font-weight: 500;
                border: none; background: transparent; cursor: pointer; transition: all .15s;
                display: inline-flex; align-items: center; gap: 0.4rem;
                {{ $activeTab === 'portfolio'
                    ? 'color:#41A2C3; border-bottom:2px solid #41A2C3; margin-bottom:-1px;'
                    : 'color: light-dark(#6b7280,#9ca3af); border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                <polyline points="16 7 22 7 22 13"/>
            </svg>
            Portfolio Growth
        </button>

        {{-- Reinsurer Metrics — oculto temporalmente, cubierto por AC vs Plan --}}
        {{-- <button
            wire:click="$set('activeTab', 'analytics')"
            style="
                padding: 10px 20px; font-size: 1rem; font-weight: 500;
                border: none; background: transparent; cursor: pointer; transition: all .15s;
                display: inline-flex; align-items: center; gap: 0.4rem;
                {{ $activeTab === 'analytics'
                    ? 'color:#41A2C3; border-bottom:2px solid #41A2C3; margin-bottom:-1px;'
                    : 'color: light-dark(#6b7280,#9ca3af); border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Reinsurer Metrics
        </button> --}}

        {{-- Portfolio Metrics --}}
        <button
            wire:click="$set('activeTab', 'budget_plan')"
            style="
                padding: 10px 20px; font-size: 1rem; font-weight: 500;
                border: none; background: transparent; cursor: pointer; transition: all .15s;
                display: inline-flex; align-items: center; gap: 0.4rem;
                {{ $activeTab === 'budget_plan'
                    ? 'color:#41A2C3; border-bottom:2px solid #41A2C3; margin-bottom:-1px;'
                    : 'color: light-dark(#6b7280,#9ca3af); border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Portfolio Metrics
        </button>

        {{-- Reinsurer Segmentation --}}
        <button
            wire:click="$set('activeTab', 'segmentation')"
            style="
                padding: 10px 20px; font-size: 1rem; font-weight: 500;
                border: none; background: transparent; cursor: pointer; transition: all .15s;
                display: inline-flex; align-items: center; gap: 0.4rem;
                {{ $activeTab === 'segmentation'
                    ? 'color:#41A2C3; border-bottom:2px solid #41A2C3; margin-bottom:-1px;'
                    : 'color: light-dark(#6b7280,#9ca3af); border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
                <path d="M22 12A10 10 0 0 0 12 2v10z"/>
            </svg>
            Reinsurer Segmentation
        </button>

        {{-- Team Performance --}}
        <button
            wire:click="$set('activeTab', 'team')"
            style="
                padding: 10px 20px; font-size: 1rem; font-weight: 500;
                border: none; background: transparent; cursor: pointer; transition: all .15s;
                display: inline-flex; align-items: center; gap: 0.4rem;
                {{ $activeTab === 'team'
                    ? 'color:#41A2C3; border-bottom:2px solid #41A2C3; margin-bottom:-1px;'
                    : 'color: light-dark(#6b7280,#9ca3af); border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Team Performance
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

    {{-- Tab 3: Reinsurer Metrics — oculto temporalmente, cubierto por AC vs Plan --}}
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

    {{-- Tab 6: Team Performance --}}
    @if ($activeTab === 'team')
        @livewire(\App\Filament\Underwritten\Widgets\TeamPerformance::class)
    @endif

</x-filament-panels::page>
