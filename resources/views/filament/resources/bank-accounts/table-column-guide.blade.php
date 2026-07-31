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
    flex: 0 0 160px;
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

    <div class="tg-section-label" x-text="lang === 'en' ? 'Always visible' : 'Siempre visible'"></div>

    <div class="tg-row">
        <div class="tg-col-name">#</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Sequential display number for the current page. Recalculates when filters or sort order change — it does not represent a permanent record ID.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número de visualización secuencial para la página actual. Se recalcula al cambiar filtros u orden — no representa un ID permanente del registro.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Status</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Current operational status of the bank account — for example, <strong>Active</strong> (ready for use) or <strong>Inactive</strong> (suspended or closed).</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Estado operacional actual de la cuenta bancaria — por ejemplo, <strong>Active</strong> (lista para usar) o <strong>Inactive</strong> (suspendida o cerrada).</div>
    </div>

    <div class="tg-section-label" style="margin-top:8px;">
        <span x-text="lang === 'en' ? 'Toggleable columns' : 'Columnas activables'"></span>
        <span x-show="lang === 'en'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (visible by default, can be hidden)</span>
        <span x-show="lang === 'es'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (visibles por defecto, se pueden ocultar)</span>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Currency</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Currency in which this account operates and receives funds (e.g. USD, EUR).</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Moneda en la que opera y recibe fondos esta cuenta (ej. USD, EUR).</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Intermediary Bank</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Correspondent bank used as an intermediate hop to route the wire transfer when there is no direct banking relationship between sender and beneficiary banks.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Banco corresponsal usado como punto intermedio para enrutar la transferencia cuando no existe relación bancaria directa entre el banco emisor y el beneficiario.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Banks (For Credit to)</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Destination bank where funds are ultimately credited. The "For Credit to" instruction tells the intermediary bank which institution receives the final deposit.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Banco de destino donde se acreditan finalmente los fondos. La instrucción "For Credit to" le indica al banco intermediario qué institución recibe el depósito final.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Beneficiary Name</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Name of the account holder at the destination bank — must match exactly what the bank has on file to avoid rejection.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre del titular de la cuenta en el banco de destino — debe coincidir exactamente con lo que tiene registrado el banco para evitar rechazos.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Beneficiary Address</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Registered address of the beneficiary account holder. Required by many jurisdictions for compliance and AML (anti-money laundering) purposes.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Dirección registrada del titular de la cuenta beneficiaria. Requerida por muchas jurisdicciones para cumplimiento normativo y AML (anti-lavado de dinero).</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Beneficiary Swift</div>
        <div class="tg-col-desc" x-show="lang === 'en'">SWIFT/BIC code of the beneficiary's bank. Uniquely identifies the bank at the international level for routing incoming wire transfers.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Código SWIFT/BIC del banco del beneficiario. Identifica de forma única al banco a nivel internacional para enrutar las transferencias entrantes.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Beneficiary Account</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Account number at the destination bank where the funds will be deposited.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número de cuenta en el banco de destino donde se depositarán los fondos.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">FFC Account Name</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Name of the final beneficiary in a <em>For Further Credit</em> (FFC) chain — used when funds pass through an intermediate account before reaching the true final recipient.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre del beneficiario final en una cadena <em>For Further Credit</em> (FFC) — se usa cuando los fondos pasan por una cuenta intermedia antes de llegar al receptor final.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">FFC Account Number</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Account number of the final FFC beneficiary. Together with the FFC Account Name, this completes the last-mile routing instruction.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número de cuenta del beneficiario final FFC. Junto con el nombre FFC, completa la instrucción de enrutamiento de la última milla.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">FFC Account Address</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Address of the final FFC beneficiary. Required by some correspondent banks to comply with FATF and local regulatory standards.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Dirección del beneficiario final FFC. Requerida por algunos bancos corresponsales para cumplir con los estándares FATF y regulatorios locales.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">
        Bank account instructions follow the chain: <strong>Intermediary Bank</strong> → <strong>For Credit to</strong> → <strong>Beneficiary Account</strong> → <strong>FFC Account</strong> (when applicable). Column visibility can be adjusted via the toggle button ( ⠿ ) in the toolbar.
    </div>
    <div class="tg-note" x-show="lang === 'es'">
        Las instrucciones de cuenta bancaria siguen la cadena: <strong>Banco intermediario</strong> → <strong>For Credit to</strong> → <strong>Cuenta beneficiaria</strong> → <strong>Cuenta FFC</strong> (cuando aplica). La visibilidad de columnas se ajusta con el botón ( ⠿ ) en la barra de herramientas.
    </div>

</div>
</div>{{-- /x-data --}}
