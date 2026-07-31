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
        <div class="tg-col-desc" x-show="lang === 'en'">System-generated unique identifier for the partner record.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Identificador único generado por el sistema para el registro del socio.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Name</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Full legal or registered name of the partner organization. This is the primary reference used when linking partners to business contracts and cost schemes.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre legal o registrado completo de la organización socia. Es la referencia principal al vincular socios con contratos de negocio y esquemas de colocación.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Short Name</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Abbreviated or commercial name of the partner. Used in space-constrained views such as cost scheme node diagrams and summary reports.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre abreviado o comercial del socio. Se usa en vistas con espacio reducido como diagramas de nodos de esquemas de colocación y reportes de resumen.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Acronym</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Short code identifying the partner — typically 2–5 characters. Appears in compact layouts and export files.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Código corto que identifica al socio — típicamente 2 a 5 caracteres. Aparece en diseños compactos y archivos de exportación.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Partner Type</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Functional classification of the partner's role in the reinsurance chain — for example, Broker, Cedant, Intermediary. Determines how the partner participates in cost scheme flows.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Clasificación funcional del rol del socio en la cadena de reaseguro — por ejemplo, Broker, Cedente, Intermediario. Determina cómo participa el socio en los flujos del esquema de colocación.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Country</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Country of domicile or primary operations of the partner. Used for geographic reporting and compliance considerations.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">País de domicilio u operaciones principales del socio. Se usa para reportes geográficos y consideraciones de cumplimiento.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">Partners are the intermediary nodes in Placement Schemes — they appear as sources or destinations in the premium flow chain between the cedant and the reinsurer.</div>
    <div class="tg-note" x-show="lang === 'es'">Los socios son los nodos intermediarios en los Esquemas de Colocación — aparecen como fuentes o destinos en la cadena de flujo de prima entre el cedente y el reasegurador.</div>

</div>
</div>
