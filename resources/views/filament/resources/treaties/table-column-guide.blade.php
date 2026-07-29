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
.tg-col-name { flex:0 0 135px; font-weight:600; font-size:12.5px; color:light-dark(#1f2937,#e5e7eb); padding-top:1px; }
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
        <div class="tg-col-name">#</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Sequential display number computed from creation order. Recalculates when the sort order changes — not a permanent identifier.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número secuencial calculado a partir del orden de creación. Se recalcula al cambiar el orden — no es un identificador permanente.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Treaty Code</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Unique alphanumeric code that identifies this treaty across the platform. Business contracts that fall under a treaty reference this code to establish the treaty relationship.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Código alfanumérico único que identifica este tratado en toda la plataforma. Los contratos de negocio que pertenecen a un tratado referencian este código para establecer la relación con el tratado.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Reinsurer</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Short name of the reinsurer that underwrites this treaty. A treaty establishes a standing reinsurance arrangement with this counterparty, under which multiple business contracts can be placed.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre corto del reasegurador que suscribe este tratado. Un tratado establece un acuerdo de reaseguro permanente con esta contraparte, bajo el cual se pueden colocar múltiples contratos de negocio.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Title</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Descriptive name of the treaty — typically includes the reinsurer name, year, type of business, and coverage scope. This is the human-readable identifier shown in business contract forms when selecting a treaty.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre descriptivo del tratado — típicamente incluye el nombre del reasegurador, año, tipo de negocio y alcance de cobertura. Es el identificador legible que aparece en los formularios de contratos de negocio al seleccionar un tratado.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Contract Type</div>
        <div class="tg-col-desc" x-show="lang === 'en'">The reinsurance structure under which this treaty operates — for example, Quota Share, Surplus, Excess of Loss. Determines how risk and premium are distributed between the cedant and the reinsurer.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">La estructura de reaseguro bajo la cual opera este tratado — por ejemplo, Cuota Parte, Excedente, Exceso de Pérdida. Determina cómo se distribuyen el riesgo y la prima entre el cedente y el reasegurador.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Description</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Free-text field capturing the scope, conditions, or notable terms of the treaty — coverage limits, territorial scope, exclusions, or any special clauses relevant to the businesses placed under it.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Campo de texto libre que captura el alcance, condiciones o términos relevantes del tratado — límites de cobertura, alcance territorial, exclusiones o cláusulas especiales relevantes para los negocios colocados bajo él.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">Treaties group related business contracts under a single standing reinsurance arrangement. A business contract linked to a treaty inherits the treaty's reinsurer and structural conditions, simplifying the placement process for repeat or standard business.</div>
    <div class="tg-note" x-show="lang === 'es'">Los tratados agrupan contratos de negocio relacionados bajo un único acuerdo de reaseguro permanente. Un contrato de negocio vinculado a un tratado hereda el reasegurador y las condiciones estructurales del tratado, simplificando el proceso de colocación para negocios repetidos o estándar.</div>

</div>
</div>
