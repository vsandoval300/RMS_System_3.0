<div
    x-data="{
        regionId: null,
        images: {
            1: 'africa',
            2: 'america',
            3: 'asia',
            4: 'europa',
            5: 'oceania',
            6: 'worldwide',
            7: 'antarctica'
        },
        names: {
            1: 'Africa',
            2: 'America',
            3: 'Asia',
            4: 'Europe',
            5: 'Oceania',
            6: 'Worldwide',
            7: 'Antarctica'
        },
        get imageFile() {
            const id = parseInt(this.regionId);
            return this.images[id] ? '/images/regions/' + this.images[id] + '.png' : null;
        },
        get regionName() {
            const id = parseInt(this.regionId);
            return this.names[id] ?? null;
        },
        init() {
            this.regionId = $wire.data?.region_id ?? null;
            this.$watch(() => $wire.data?.region_id, v => { this.regionId = v; });
        }
    }"
    x-init="init()"
    style="margin-top: 0.75rem;"
>
    {{-- Placeholder when no region is selected --}}
    <div
        x-show="!imageFile"
        x-cloak
        style="
            width: 100%; aspect-ratio: 2/1;
            border-radius: 10px;
            border: 1px dashed light-dark(rgba(0,0,0,0.12), rgba(255,255,255,0.08));
            display: flex; align-items: center; justify-content: center;
            color: light-dark(#9ca3af, #4b5563);
            font-size: 0.85rem;
        "
    >
        Select a region to display the map
    </div>

    {{-- Region image --}}
    <img
        x-show="imageFile"
        x-cloak
        :src="imageFile"
        :alt="regionName + ' map'"
        style="
            display: block;
            width: 100%;
            border-radius: 10px;
            border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.06));
            transition: opacity 0.25s ease;
        "
    />

    {{-- Label --}}
    <div style="text-align: center; margin-top: 0.65rem; font-size: 0.82rem; min-height: 1.4em; color: light-dark(#6b7280,#9ca3af);">
        <span x-show="!regionName" x-cloak>Select a region to highlight geographic coverage</span>
        <span
            x-show="regionName"
            x-cloak
            style="font-weight: 600; color: #41A2C3; display: flex; align-items: center; justify-content: center; gap: 0.4rem;"
        >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="10" r="3"/>
                <path d="M12 2a8 8 0 0 1 8 8c0 5.4-7.2 12-8 12S4 15.4 4 10a8 8 0 0 1 8-8z"/>
            </svg>
            <span x-text="regionName + ' — geographic coverage'"></span>
        </span>
    </div>
</div>
