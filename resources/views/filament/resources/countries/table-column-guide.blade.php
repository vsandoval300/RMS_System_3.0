<style>
.cg-wrap{font-family:inherit;font-size:13px;line-height:1.6;}
.cg-section-title{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:light-dark(#9ca3af,#6b7280);padding:14px 0 7px;border-bottom:1px solid light-dark(#e5e7eb,#374151);margin-bottom:10px;}
.cg-section-title:first-of-type{padding-top:0;}
.cg-col-list{list-style:none;padding:0;margin:0 0 4px;display:flex;flex-direction:column;gap:10px;}
.cg-col-item{display:flex;flex-direction:column;gap:2px;}
.cg-col-name{font-weight:700;font-size:12.5px;color:light-dark(#111827,#f9fafb);}
.cg-col-desc{color:light-dark(#6b7280,#9ca3af);font-size:12px;}
.cg-badge{display:inline-block;font-size:10px;font-weight:600;padding:1px 7px;border-radius:999px;background:light-dark(#f3f4f6,#27272a);color:light-dark(#6b7280,#9ca3af);margin-left:4px;vertical-align:middle;}
.cg-tip{font-size:12px;color:light-dark(#6b7280,#9ca3af);padding:9px 12px;background:light-dark(#f9fafb,#111827);border-left:3px solid #41A2C3;border-radius:0 4px 4px 0;margin-top:10px;}
</style>

<div class="cg-wrap" x-data="{ lang: $store.cgLang ?? 'en' }">

    <div style="display:flex;justify-content:flex-end;margin-bottom:14px;">
        <div style="display:flex;border:1px solid light-dark(#d1d5db,#374151);border-radius:999px;overflow:hidden;">
            <button type="button" @click="lang='en'" :style="lang==='en'?'background:#41A2C3;color:#fff;':''" style="padding:3px 12px;font-size:11px;font-weight:700;border:none;cursor:pointer;background:transparent;color:light-dark(#6b7280,#9ca3af);transition:all .15s;">EN</button>
            <button type="button" @click="lang='es'" :style="lang==='es'?'background:#41A2C3;color:#fff;':''" style="padding:3px 12px;font-size:11px;font-weight:700;border:none;cursor:pointer;background:transparent;color:light-dark(#6b7280,#9ca3af);transition:all .15s;">ES</button>
        </div>
    </div>

    <div class="cg-section-title" x-text="lang==='en'?'Always visible columns':'Columnas siempre visibles'"></div>

    <ul class="cg-col-list">
        <li class="cg-col-item">
            <span class="cg-col-name">ID</span>
            <span class="cg-col-desc" x-show="lang==='en'">Unique numeric identifier for the country record.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Identificador numérico único del registro de país.</span>
        </li>
        <li class="cg-col-item">
            <span class="cg-col-name" x-text="lang==='en'?'Name':'Nombre'"></span>
            <span class="cg-col-desc" x-show="lang==='en'">Full official name of the country (e.g., United States of America, Mexico, Spain).</span>
            <span class="cg-col-desc" x-show="lang==='es'">Nombre oficial completo del país (ej. Estados Unidos de América, México, España).</span>
        </li>
        <li class="cg-col-item">
            <span class="cg-col-name" x-text="lang==='en'?'Region':'Región'"></span>
            <span class="cg-col-desc" x-show="lang==='en'">Geographic macro-region this country belongs to (e.g., Americas, Europe, Asia Pacific).</span>
            <span class="cg-col-desc" x-show="lang==='es'">Macro-región geográfica a la que pertenece el país (ej. Américas, Europa, Asia Pacífico).</span>
        </li>
    </ul>

    <div class="cg-section-title" x-text="lang==='en'?'Toggleable columns (hidden by default)':'Columnas ocultables (ocultas por defecto)'"></div>

    <ul class="cg-col-list">
        <li class="cg-col-item">
            <span class="cg-col-name">Alpha 2</span>
            <span class="cg-col-desc" x-show="lang==='en'">ISO 3166-1 alpha-2 two-letter country code (e.g., US, MX, ES). Widely used in international standards and APIs.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Código de país de dos letras ISO 3166-1 alpha-2 (ej. US, MX, ES). Ampliamente usado en estándares internacionales y APIs.</span>
        </li>
        <li class="cg-col-item">
            <span class="cg-col-name">Alpha 3</span>
            <span class="cg-col-desc" x-show="lang==='en'">ISO 3166-1 alpha-3 three-letter country code (e.g., USA, MEX, ESP). Provides an unambiguous country identifier used in finance and legal documents.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Código de país de tres letras ISO 3166-1 alpha-3 (ej. USA, MEX, ESP). Proporciona un identificador de país sin ambigüedades usado en finanzas y documentos legales.</span>
        </li>
        <li class="cg-col-item">
            <span class="cg-col-name">Country Code</span>
            <span class="cg-col-desc" x-show="lang==='en'">ISO 3166-1 numeric country code (e.g., 840 for USA, 484 for Mexico). A three-digit numeric code used as an alternative to letter-based codes.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Código numérico de país ISO 3166-1 (ej. 840 para EUA, 484 para México). Código numérico de tres dígitos usado como alternativa a los códigos alfabéticos.</span>
        </li>
        <li class="cg-col-item">
            <span class="cg-col-name">ISO Code</span>
            <span class="cg-col-desc" x-show="lang==='en'">Additional ISO reference code used internally for cross-system compatibility.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Código de referencia ISO adicional usado internamente para compatibilidad entre sistemas.</span>
        </li>
        <li class="cg-col-item">
            <span class="cg-col-name">AM Best Code</span>
            <span class="cg-col-desc" x-show="lang==='en'">Country code assigned by AM Best, the insurance credit rating agency. Used when referencing countries in reinsurance credit risk evaluations.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Código de país asignado por AM Best, la agencia calificadora de crédito para seguros. Se usa al referenciar países en evaluaciones de riesgo crediticio de reaseguro.</span>
        </li>
        <li class="cg-col-item">
            <span class="cg-col-name">Latitude / Longitude</span>
            <span class="cg-col-desc" x-show="lang==='en'">Geographic coordinates of the country's reference point. Used for geographic display and spatial lookups.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Coordenadas geográficas del punto de referencia del país. Usadas para visualización geográfica y búsquedas espaciales.</span>
        </li>
    </ul>

    <div class="cg-tip" x-show="lang==='en'">Columns marked as toggleable are hidden by default to keep the table compact. Use the column toggle button (⊞) in the toolbar to show or hide them.</div>
    <div class="cg-tip" x-show="lang==='es'">Las columnas marcadas como ocultables están ocultas por defecto para mantener la tabla compacta. Usa el botón de columnas (⊞) en la barra de herramientas para mostrarlas u ocultarlas.</div>

</div>
