<div x-data="{ lang: 'en' }">

<style>
.tg-wrap {
    font-family: inherit;
    font-size: 13px;
    line-height: 1.5;
}
.tg-lang-toggle {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 14px;
}
.tg-lang-btn-wrap {
    display: flex;
    border: 1px solid light-dark(#d1d5db, #374151);
    border-radius: 999px;
    overflow: hidden;
}
.tg-lang-btn {
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    border: none;
    cursor: pointer;
    transition: all .15s;
    background: transparent;
    color: light-dark(#6b7280, #9ca3af);
}
.tg-section-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: light-dark(#9ca3af, #6b7280);
    padding: 16px 0 6px;
    border-bottom: 1px solid light-dark(#e5e7eb, #374151);
    margin-bottom: 4px;
}
.tg-section-label:first-of-type {
    padding-top: 0;
}
.tg-row {
    display: flex;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid light-dark(#f3f4f6, #1f2937);
    align-items: flex-start;
}
.tg-row:last-child {
    border-bottom: none;
}
.tg-col-name {
    flex: 0 0 140px;
    font-weight: 600;
    font-size: 12.5px;
    color: light-dark(#1f2937, #e5e7eb);
    padding-top: 1px;
}
.tg-col-desc {
    flex: 1;
    color: light-dark(#4b5563, #9ca3af);
    font-size: 12.5px;
}
.tg-hidden-badge {
    display: inline-block;
    font-size: 10px;
    font-weight: 600;
    padding: 1px 7px;
    border-radius: 9999px;
    background: light-dark(#f3f4f6, #1f2937);
    color: light-dark(#6b7280, #9ca3af);
    margin-left: 6px;
    vertical-align: middle;
    letter-spacing: 0.03em;
}
.tg-note {
    font-size: 13px;
    color: light-dark(#6b7280, #9ca3af);
    margin-top: 16px;
    padding: 10px 12px;
    background: light-dark(#f9fafb, #111827);
    border-left: 3px solid light-dark(#d1d5db, #374151);
    border-radius: 0 4px 4px 0;
}
</style>

{{-- Language toggle --}}
<div class="tg-lang-toggle">
    <div class="tg-lang-btn-wrap">
        <button type="button" class="tg-lang-btn" @click="lang = 'en'"
            :style="lang === 'en' ? 'background:#41A2C3; color:#ffffff;' : ''">EN</button>
        <button type="button" class="tg-lang-btn" @click="lang = 'es'"
            :style="lang === 'es' ? 'background:#41A2C3; color:#ffffff;' : ''">ES</button>
    </div>
</div>

<div class="tg-wrap">

    {{-- ── Always visible ─────────────────────────────────────────── --}}
    <div class="tg-section-label" x-text="lang === 'en' ? 'Always visible' : 'Siempre visible'"></div>

    <div class="tg-row">
        <div class="tg-col-name">#</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Running row number across the current page.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número de fila consecutivo dentro de la página actual.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Reinsurer</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Short name of the Reinsurer associated with the Business to which the Operative Document belongs.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre corto del Reasegurador asociado al Negocio al que pertenece el Documento Operativo.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Document</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Operative Document the Instalment belongs to.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Documento Operativo al que pertenece el Instalment.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Index</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Position of the Instalment within the Operative Document's payment schedule, shown as "n of total".</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Posición del Instalment dentro del calendario de pagos del Documento Operativo, mostrada como "n de total".</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Proportion</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Percentage of the total premium this instalment represents.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Porcentaje de la prima total que representa este instalment.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Exch Rate</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Exchange rate applied to convert the instalment into US Dollars.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Tipo de cambio aplicado para convertir el instalment a Dólares Americanos.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Due Date</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Date on which the Instalment is due. Shown in red with a warning icon, plus a "+N / −N days" note, when the date has passed and the instalment is not yet Completed.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Fecha en la que vence el Instalment. Se muestra en rojo con un ícono de advertencia, además de una nota "+N / −N días", cuando la fecha ya pasó y el instalment no está Completed.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Type</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Transaction Type description.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Descripción del Tipo de Transacción.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Net Amount</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Net amount of the Instalment's final settlement leg (the last row of its ledger).</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Monto neto de la última etapa de liquidación del Instalment (el último renglón de su ledger).</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Status</div>
        <div class="tg-col-desc" x-show="lang === 'en'">
            Lifecycle status badge:<br>
            <strong>Pending</strong> · <strong>In process</strong> · <strong>Completed</strong>.
        </div>
        <div class="tg-col-desc" x-show="lang === 'es'">
            Insignia de estado del ciclo de vida:<br>
            <strong>Pending</strong> · <strong>In process</strong> · <strong>Completed</strong>.
        </div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Progress</div>
        <div class="tg-col-desc" x-show="lang === 'en'">A row of dots — one per ledger line — showing how far the instalment's settlement has advanced, followed by a percentage.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Una fila de puntos — uno por cada renglón del ledger — que muestra qué tan avanzada está la liquidación del instalment, seguida de un porcentaje.</div>
    </div>

    {{-- ── Hidden by default ───────────────────────────────────────── --}}
    <div class="tg-section-label" style="margin-top:8px;">
        <span x-text="lang === 'en' ? 'Hidden by default' : 'Ocultas por defecto'"></span>
        <span x-show="lang === 'en'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (toggle via column selector)</span>
        <span x-show="lang === 'es'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (activar desde el selector de columnas)</span>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Id Transaction <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">Internal identifier (UUID) of the Transaction record. Copyable.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Identificador interno (UUID) del registro de Transacción. Se puede copiar.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Remittance <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">Remittance code associated with the Instalment, when one has been assigned.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Código de remesa asociado al Instalment, cuando se ha asignado uno.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Created At <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">Date and time the Transaction record was created.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Fecha y hora en que se creó el registro de la Transacción.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Updated At <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">Date and time the Transaction record was last updated.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Fecha y hora de la última actualización del registro de la Transacción.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Deleted At <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">Date and time the Transaction record was deleted, for soft-deleted instalments.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Fecha y hora en que se eliminó el registro de la Transacción, para instalments eliminados de forma reversible.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">
        Columns marked <strong>hidden</strong> can be shown or hidden via the column toggle button ( ⠿ ) in the table toolbar. Your selection is saved per session.
    </div>
    <div class="tg-note" x-show="lang === 'es'">
        Las columnas marcadas como <strong>hidden</strong> se pueden mostrar u ocultar mediante el botón de columnas ( ⠿ ) en la barra de herramientas de la tabla. Tu selección se guarda por sesión.
    </div>

</div>
</div>{{-- /x-data --}}
