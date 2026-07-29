@php
    use Filament\Widgets\View\Components\ChartWidgetComponent;
    use Illuminate\View\ComponentAttributeBag;

    $color      = $this->getColor();
    $heading    = $this->getHeading();
    $description = $this->getDescription();
    $filters    = $this->getFilters();
    $isCollapsible = $this->isCollapsible();
    $type       = $this->getType();
    $maxHeight  = $this->getMaxHeight();
    $hasMaxHeight = filled($maxHeight) && $maxHeight !== '100%';
@endphp

<x-filament-widgets::widget class="fi-wi-chart" x-data="{ chartGuideOpen: false }">
    <x-filament::section
        :description="$description"
        :heading="$heading"
        :collapsible="$isCollapsible"
    >
        <x-slot name="afterHeader">
            {{-- Guide button --}}
            <button
                type="button"
                x-on:click="chartGuideOpen = true"
                style="display:inline-flex; align-items:center; gap:0.35rem; font-size:0.78rem; font-weight:600; padding:0.3rem 0.75rem; border-radius:9999px; border:1px solid light-dark(#d1d5db,#374151); background:transparent; color:light-dark(#6b7280,#9ca3af); cursor:pointer; transition:all .15s;"
                onmouseover="this.style.borderColor='#41A2C3'; this.style.color='#41A2C3';"
                onmouseout="this.style.borderColor=''; this.style.color='';"
            >
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Guide
            </button>

            {{-- Existing filters --}}
            @if ($filters)
                <x-filament::input.wrapper inline-prefix wire:target="filter" class="fi-wi-chart-filter">
                    <x-filament::input.select inline-prefix wire:model.live="filter">
                        @foreach ($filters as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            @endif

            @if (method_exists($this, 'getFiltersSchema'))
                <x-filament::dropdown placement="bottom-end" shift width="xs" class="fi-wi-chart-filter">
                    <x-slot name="trigger">{{ $this->getFiltersTriggerAction() }}</x-slot>
                    <div class="fi-wi-chart-filter-content">
                        {{ $this->getFiltersSchema() }}
                        @if (method_exists($this, 'hasDeferredFilters') && $this->hasDeferredFilters())
                            <div class="fi-wi-chart-filter-content-actions-ctn">
                                {{ $this->getFiltersApplyAction() }}
                                {{ $this->getFiltersResetAction() }}
                            </div>
                        @endif
                    </div>
                </x-filament::dropdown>
            @endif
        </x-slot>

        {{-- Chart canvas --}}
        <div @if ($pollingInterval = $this->getPollingInterval()) wire:poll.{{ $pollingInterval }}="updateChartData" @endif>
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                data-chart-type="{{ $type }}"
                x-data="chart({
                    cachedData: @js($this->getCachedData()),
                    options: @js($this->getOptions()),
                    type: @js($type),
                })"
                {{
                    (new ComponentAttributeBag)
                        ->color(ChartWidgetComponent::class, $color)
                        ->class([
                            'fi-wi-chart-canvas-ctn',
                            'fi-wi-chart-canvas-ctn-no-aspect-ratio' => $hasMaxHeight,
                        ])
                }}
            >
                <canvas x-ref="canvas" @style(['width: 100%', 'height: 100%; max-height: 100%' => ! $hasMaxHeight, "max-height: {$maxHeight}" => $hasMaxHeight])></canvas>
                <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                <span x-ref="borderColorElement"     class="fi-wi-chart-border-color"></span>
                <span x-ref="gridColorElement"       class="fi-wi-chart-grid-color"></span>
                <span x-ref="textColorElement"       class="fi-wi-chart-text-color"></span>
            </div>
        </div>
    </x-filament::section>

    {{-- Guide backdrop --}}
    <div
        x-show="chartGuideOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-on:click="chartGuideOpen = false"
        style="position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:49;"
        x-cloak
    ></div>

    {{-- Guide slide-over panel --}}
    <div
        x-show="chartGuideOpen"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="transform translate-x-full"
        x-transition:enter-end="transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="transform translate-x-0"
        x-transition:leave-end="transform translate-x-full"
        style="position:fixed; top:0; right:0; bottom:0; width:480px; max-width:92vw; z-index:50;"
        x-cloak
        x-data="{ lang: 'en' }"
    >
        <div style="display:flex; flex-direction:column; height:100%; background:light-dark(#ffffff,#18181b); border-left:1px solid light-dark(#e5e7eb,#27272a); box-shadow:-8px 0 32px rgba(0,0,0,0.18);">

            {{-- Panel header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 20px 14px; border-bottom:1px solid light-dark(#e5e7eb,#27272a); flex-shrink:0;">
                <span style="font-size:15px; font-weight:700; color:light-dark(#111827,#f9fafb);" x-text="lang === 'en' ? 'Understanding This Chart' : 'Entendiendo este gráfico'"></span>
                <div style="display:flex; align-items:center; gap:8px;">
                    {{-- Language toggle --}}
                    <div style="display:flex; border:1px solid light-dark(#d1d5db,#374151); border-radius:999px; overflow:hidden;">
                        <button type="button"
                            @click="lang = 'en'"
                            :style="lang === 'en' ? 'background:#41A2C3; color:#fff;' : ''"
                            style="padding:3px 10px; font-size:11px; font-weight:700; border:none; cursor:pointer; background:transparent; color:light-dark(#6b7280,#9ca3af); transition:all .15s;">EN</button>
                        <button type="button"
                            @click="lang = 'es'"
                            :style="lang === 'es' ? 'background:#41A2C3; color:#fff;' : ''"
                            style="padding:3px 10px; font-size:11px; font-weight:700; border:none; cursor:pointer; background:transparent; color:light-dark(#6b7280,#9ca3af); transition:all .15s;">ES</button>
                    </div>
                    {{-- Close button --}}
                    <button type="button" @click="chartGuideOpen = false"
                        style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:6px; border:1px solid light-dark(#e5e7eb,#374151); background:transparent; cursor:pointer; color:light-dark(#6b7280,#9ca3af); transition:all .15s;"
                        onmouseover="this.style.borderColor='#41A2C3'; this.style.color='#41A2C3';"
                        onmouseout="this.style.borderColor=''; this.style.color='';">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>

            {{-- Scrollable body --}}
            <div style="flex:1; min-height:0; overflow-y:auto; padding:20px;">
                @include('filament.resources.businesses.chart-guide')
            </div>

        </div>
    </div>

</x-filament-widgets::widget>
