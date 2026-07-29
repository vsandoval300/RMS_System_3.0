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
        <div class="tg-col-name">#</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Sequential display number computed from insertion order. Recalculates on sort changes — it is not a permanent ID.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número secuencial calculado a partir del orden de inserción. Se recalcula al cambiar el orden — no es un ID permanente.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">ID</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Unique internal identifier of the placement scheme. Used as the primary key when assigning this scheme to operative documents.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Identificador interno único del esquema de colocación. Se usa como clave primaria al asignar este esquema a documentos operativos.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Daily Index</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Numeric index applied as a daily rate multiplier in premium calculations. A higher index results in a proportionally higher computed premium. Used in time-based or indexed premium structures.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Índice numérico aplicado como multiplicador de tasa diaria en el cálculo de primas. Un índice más alto resulta en una prima calculada proporcionalmente mayor. Se usa en estructuras de prima basadas en tiempo o indexadas.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Share</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Percentage of the total premium that flows through this placement scheme. For example, 100% means the full gross premium is processed through this scheme; 50% means it handles half.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Porcentaje de la prima total que fluye a través de este esquema de colocación. Por ejemplo, 100% significa que la prima bruta total se procesa a través de este esquema; 50% significa que maneja la mitad.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Agreement Type</div>
        <div class="tg-col-desc" x-show="lang === 'en'">The type of reinsurance agreement structure this scheme applies to — for example, Facultative, Proportional Treaty, Non-Proportional. Determines the calculation logic for deductions and net amounts in the cost nodes.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">El tipo de estructura de acuerdo de reaseguro al que aplica este esquema — por ejemplo, Facultativo, Tratado proporcional, No proporcional. Determina la lógica de cálculo para las deducciones y montos netos en los nodos de costo.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Created At</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Relative time since the scheme was created — shown as "X days/months ago". Useful for identifying recently added schemes.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Tiempo relativo desde que se creó el esquema — mostrado como "hace X días/meses". Útil para identificar esquemas agregados recientemente.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Updated At</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Relative time since the last modification to this scheme. Helps identify schemes that have been recently revised.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Tiempo relativo desde la última modificación a este esquema. Ayuda a identificar esquemas que han sido revisados recientemente.</div>
    </div>

    <div class="tg-section-label" style="margin-top:8px;">
        <span x-text="lang === 'en' ? 'Hidden by default' : 'Ocultas por defecto'"></span>
        <span x-show="lang === 'en'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (toggle via column selector)</span>
        <span x-show="lang === 'es'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (activar desde el selector de columnas)</span>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Created By <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">The user who created this placement scheme.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">El usuario que creó este esquema de colocación.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">A Placement Scheme defines the premium flow structure — how gross premium is split, what deductions apply, and which parties (partners) receive net amounts. Each scheme contains a tree of Cost Nodes visible when you open the record.</div>
    <div class="tg-note" x-show="lang === 'es'">Un Esquema de Colocación define la estructura de flujo de prima — cómo se divide la prima bruta, qué deducciones aplican y qué partes (socios) reciben los montos netos. Cada esquema contiene un árbol de Nodos de Costo visible al abrir el registro.</div>

</div>
</div>
