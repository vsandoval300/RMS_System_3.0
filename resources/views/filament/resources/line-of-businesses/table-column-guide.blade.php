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
.tg-col-name { flex:0 0 130px; font-weight:600; font-size:12.5px; color:light-dark(#1f2937,#e5e7eb); padding-top:1px; }
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
        <div class="tg-col-desc" x-show="lang === 'en'">System-generated unique identifier for the line of business record.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Identificador único generado por el sistema para el registro del ramo de negocio.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Name</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Name of the insurance line of business — the broadest category used to classify coverages, businesses, and treaties. Examples: Property, Casualty, Marine, Aviation, Life.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre del ramo de negocio de seguros — la categoría más amplia usada para clasificar coberturas, negocios y tratados. Ejemplos: Propiedad, Accidentes, Marina, Aviación, Vida.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Description</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Detailed explanation of the types of risks and perils this line encompasses, its regulatory scope, and how it is typically structured in reinsurance arrangements.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Explicación detallada de los tipos de riesgos y peligros que abarca este ramo, su alcance regulatorio, y cómo se estructura típicamente en los arreglos de reaseguro.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Risk Covered</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Summary of the specific risk categories or perils covered under this line of business. Used as a reference tag when classifying contracts and building reinsurance programs.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Resumen de las categorías de riesgo o peligros específicos cubiertos bajo este ramo de negocio. Se usa como etiqueta de referencia al clasificar contratos y construir programas de reaseguro.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">Lines of Business are the parent classification for <strong>Coverages</strong>. Each coverage belongs to exactly one line of business. Changes here affect how coverages are grouped throughout the platform.</div>
    <div class="tg-note" x-show="lang === 'es'">Los Ramos de Negocio son la clasificación padre de las <strong>Coberturas</strong>. Cada cobertura pertenece exactamente a un ramo de negocio. Los cambios aquí afectan la forma en que las coberturas se agrupan en toda la plataforma.</div>

</div>
</div>
