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
        <div class="tg-col-name">ID</div>
        <div class="tg-col-desc" x-show="lang === 'en'">System-generated unique identifier for the coverage record. Referenced when linking coverages to business contracts.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Identificador único generado por el sistema para el registro de cobertura. Se referencia al vincular coberturas con contratos de negocio.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Name</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Full name of the risk coverage category — for example, Property Damage, General Liability, Marine Cargo. This is the label shown in business contracts and reports.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre completo de la categoría de cobertura de riesgo — por ejemplo, Daño a la propiedad, Responsabilidad civil, Carga marítima. Es la etiqueta que aparece en los contratos de negocio y reportes.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Acronym</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Short code for the coverage — typically 2–5 characters. This acronym appears as a badge tag on the Business list to quickly identify which coverages a contract includes.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Código corto de la cobertura — típicamente 2 a 5 caracteres. Este acrónimo aparece como etiqueta badge en la lista de negocios para identificar rápidamente qué coberturas incluye un contrato.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Description</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Detailed explanation of what risks or perils this coverage category encompasses. Helps users select the correct coverage when creating or reviewing business contracts.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Explicación detallada de qué riesgos o peligros abarca esta categoría de cobertura. Ayuda a los usuarios a seleccionar la cobertura correcta al crear o revisar contratos de negocio.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Line of Business</div>
        <div class="tg-col-desc" x-show="lang === 'en'">The broader insurance line this coverage belongs to — for example, Property, Casualty, Marine. Coverages are grouped by line of business in this table for easier navigation.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">El ramo de seguros más amplio al que pertenece esta cobertura — por ejemplo, Propiedad, Accidentes, Marina. Las coberturas se agrupan por ramo de negocio en esta tabla para facilitar la navegación.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">Records are grouped by <strong>Line of Business</strong> by default. Click a group header to collapse or expand it. Use the search bar to locate a specific coverage by name, acronym, or description.</div>
    <div class="tg-note" x-show="lang === 'es'">Los registros se agrupan por <strong>Line of Business</strong> de forma predeterminada. Haz clic en el encabezado del grupo para contraerlo o expandirlo. Usa la barra de búsqueda para localizar una cobertura específica por nombre, acrónimo o descripción.</div>

</div>
</div>
