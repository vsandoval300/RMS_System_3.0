<div style="display:flex; align-items:center; gap:12px; width:100%;">
    <img
        src="{{ asset('images/logo5.png') }}"
        alt="RMS Platform"
        style="height:40px; object-fit:contain; object-position:left center; flex:1; min-width:0;"
    >
    <div style="width:1px; height:32px; background:light-dark(rgba(0,0,0,0.15), rgba(255,255,255,0.15)); flex-shrink:0;"></div>
    <img
        src="{{ asset('images/Integrity_2.png') }}"
        alt="Integrity"
        class="rms-brand-logo--light"
        style="height:46px; object-fit:contain; object-position:right center; flex-shrink:0;"
    >
    <img
        src="{{ asset('images/Integrity.png') }}"
        alt="Integrity"
        class="rms-brand-logo--dark"
        style="height:46px; object-fit:contain; object-position:right center; flex-shrink:0; opacity:0.9;"
    >
</div>

<style>
    /* Default: light mode */
    .rms-brand-logo--light { display: block; }
    .rms-brand-logo--dark  { display: none; }

    /* OS dark preference (before Filament sets the class) */
    @media (prefers-color-scheme: dark) {
        .rms-brand-logo--light { display: none; }
        .rms-brand-logo--dark  { display: block; }
    }

    /* Filament dark mode (.dark class on <html>) — higher specificity, always wins */
    .dark .rms-brand-logo--light { display: none; }
    .dark .rms-brand-logo--dark  { display: block; }

    /* Filament light mode explicitly set — overrides OS media query */
    html:not(.dark) .rms-brand-logo--light { display: block; }
    html:not(.dark) .rms-brand-logo--dark  { display: none; }
</style>
