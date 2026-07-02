<x-filament-panels::page>

    {{-- Tab bar --}}
    <div style="
        display: flex;
        gap: 0;
        border-bottom: 1px solid light-dark(rgba(0,0,0,0.1), rgba(255,255,255,0.1));
        margin-bottom: 1.5rem;
    ">
        <button
            wire:click="$set('activeTab', 'overview')"
            style="
                padding: 10px 20px;
                font-size: 1rem;
                font-weight: 500;
                border: none;
                background: transparent;
                cursor: pointer;
                transition: all .15s;
                {{ $activeTab === 'overview'
                    ? 'color:#41A2C3; border-bottom:2px solid #41A2C3; margin-bottom:-1px;'
                    : 'color: light-dark(#6b7280,#9ca3af); border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
        >
            Underwritten Overview
        </button>

        <button
            wire:click="$set('activeTab', 'analytics')"
            style="
                padding: 10px 20px;
                font-size: 1rem;
                font-weight: 500;
                border: none;
                background: transparent;
                cursor: pointer;
                transition: all .15s;
                {{ $activeTab === 'analytics'
                    ? 'color:#41A2C3; border-bottom:2px solid #41A2C3; margin-bottom:-1px;'
                    : 'color: light-dark(#6b7280,#9ca3af); border-bottom:2px solid transparent; margin-bottom:-1px;' }}"
        >
            Analytics
        </button>
    </div>

    {{-- Tab 1: Underwritten Overview --}}
    @if ($activeTab === 'overview')
        @livewire(\App\Filament\Underwritten\Widgets\UnderwrittenOverview::class)
        @livewire(\App\Filament\Underwritten\Widgets\UnderwrittenProfile::class)
    @endif

    {{-- Tab 2: Analytics --}}
    @if ($activeTab === 'analytics')
        @livewire(\App\Filament\Underwritten\Widgets\ReinsurerPremiumComparison::class)
    @endif

</x-filament-panels::page>
