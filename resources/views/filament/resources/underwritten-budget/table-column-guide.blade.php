<div x-data="{ lang: 'en' }">
<style>
.tg-wrap { font-family:inherit; font-size:13px; line-height:1.5; }
.tg-lang-toggle { display:flex; justify-content:flex-end; margin-bottom:14px; }
.tg-lang-btn-wrap { display:flex; border:1px solid light-dark(#d1d5db,#374151); border-radius:999px; overflow:hidden; }
.tg-lang-btn { padding:4px 12px; font-size:11px; font-weight:700; letter-spacing:0.05em; border:none; cursor:pointer; transition:all .15s; background:transparent; color:light-dark(#6b7280,#9ca3af); }
.tg-section-label { font-size:10px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:light-dark(#9ca3af,#6b7280); padding:16px 0 6px; border-bottom:1px solid light-dark(#e5e7eb,#374151); margin-bottom:4px; }
.tg-section-label:first-of-type { padding-top:0; }
.tg-row { display:flex; gap:12px; padding:10px 0; border-bottom:1px solid light-dark(#f3f4f6,#1f2937); align-items:flex-start; }
.tg-row:last-child { border-bottom:none; }
.tg-col-name { flex:0 0 140px; font-weight:600; font-size:12.5px; color:light-dark(#1f2937,#e5e7eb); padding-top:1px; }
.tg-col-desc { flex:1; color:light-dark(#4b5563,#9ca3af); font-size:12.5px; }
.tg-hidden-badge { display:inline-block; font-size:10px; font-weight:600; padding:1px 7px; border-radius:9999px; background:light-dark(#f3f4f6,#1f2937); color:light-dark(#6b7280,#9ca3af); margin-left:6px; vertical-align:middle; letter-spacing:0.03em; }
.tg-note { font-size:13px; color:light-dark(#6b7280,#9ca3af); margin-top:16px; padding:10px 12px; background:light-dark(#f9fafb,#111827); border-left:3px solid light-dark(#d1d5db,#374151); border-radius:0 4px 4px 0; }
</style>

<div class="tg-lang-toggle">
    <div class="tg-lang-btn-wrap">
        <button type="button" class="tg-lang-btn" @click="lang = 'en'" :style="lang === 'en' ? 'background:#41A2C3; color:#ffffff;' : ''">EN</button>
        <button type="button" class="tg-lang-btn" @click="lang = 'es'" :style="lang === 'es' ? 'background:#41A2C3; color:#ffffff;' : ''">ES</button>
    </div>
</div>

<div class="tg-wrap">

    <div class="tg-section-label" x-text="lang === 'en' ? 'Always visible' : 'Siempre visible'"></div>

    <div class="tg-row">
        <div class="tg-col-name">Year</div>
        <div class="tg-col-desc" x-show="lang === 'en'">The fiscal or underwriting year this budget version applies to. Displayed as a badge for quick visual scanning.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">El año fiscal o de suscripción al que aplica esta versión de presupuesto. Se muestra como badge para identificación visual rápida.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Version</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Revision number within the same year — <strong>v1</strong> is the initial budget, <strong>v2</strong> the first revision, and so on. A year can have multiple versions as targets are adjusted throughout the cycle.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número de revisión dentro del mismo año — <strong>v1</strong> es el presupuesto inicial, <strong>v2</strong> la primera revisión, etc. Un año puede tener varias versiones a medida que los objetivos se ajustan durante el ciclo.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Version Label</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Free-text name assigned to this version to distinguish it from others — e.g. "Initial Plan", "Mid-Year Revision", "Board Approved".</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre en texto libre asignado a esta versión para distinguirla de otras — ej. "Plan inicial", "Revisión de mitad de año", "Aprobado por el Consejo".</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Reinsurers</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Count of reinsurers included in this budget version. Each line item in the budget represents a target allocation per reinsurer.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Cantidad de reaseguradores incluidos en esta versión de presupuesto. Cada línea del presupuesto representa una asignación objetivo por reasegurador.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Total Budget (USD)</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Sum of all reinsurer premium budget targets in this version, expressed in USD. This is the team's aggregate production goal for the year.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Suma de todos los objetivos de prima por reasegurador en esta versión, expresada en USD. Es la meta de producción agregada del equipo para el año.</div>
    </div>

    <div class="tg-section-label" style="margin-top:8px;">
        <span x-text="lang === 'en' ? 'Hidden by default' : 'Ocultas por defecto'"></span>
        <span x-show="lang === 'en'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (toggle via column selector)</span>
        <span x-show="lang === 'es'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (activar desde el selector de columnas)</span>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Created By <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">The user who created this budget version.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">El usuario que creó esta versión de presupuesto.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Date <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">Date the budget version record was created in the system.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Fecha en que se creó el registro de esta versión de presupuesto en el sistema.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">The table is sorted by Year descending by default — the most recent budget year appears first. Use the Year filter to focus on a specific cycle.</div>
    <div class="tg-note" x-show="lang === 'es'">La tabla se ordena por Year descendente de forma predeterminada — el año de presupuesto más reciente aparece primero. Usa el filtro Year para enfocarte en un ciclo específico.</div>

</div>
</div>
