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
        <div class="tg-col-name">ID</div>
        <div class="tg-col-desc" x-show="lang === 'en'">System-generated unique identifier for the bank record. Used for internal cross-references and integrations.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Identificador único generado por el sistema para el registro del banco. Se usa para referencias cruzadas internas e integraciones.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Name</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Full registered name of the bank institution.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre registrado completo de la institución bancaria.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Address</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Registered address or headquarters location of the bank. Required for international wire transfer instructions.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Dirección registrada o sede principal del banco. Requerida para instrucciones de transferencia bancaria internacional.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">ABA Number</div>
        <div class="tg-col-desc" x-show="lang === 'en'">American Bankers Association routing number — a 9-digit code that identifies the bank for domestic US wire transfers and ACH payments.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número de ruta de la American Bankers Association — un código de 9 dígitos que identifica al banco para transferencias internas en EE.UU. y pagos ACH.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">SWIFT Code</div>
        <div class="tg-col-desc" x-show="lang === 'en'">International bank identifier code (also called BIC). Used for routing cross-border wire transfers. Format: 8 or 11 characters identifying bank, country, location, and branch.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Código internacional de identificación bancaria (también llamado BIC). Se usa para enrutar transferencias internacionales. Formato: 8 u 11 caracteres que identifican banco, país, ubicación y sucursal.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">
        Use the search bar to quickly locate a bank by name, ABA number, or SWIFT code. Column visibility can be adjusted via the toggle button ( ⠿ ) in the table toolbar.
    </div>
    <div class="tg-note" x-show="lang === 'es'">
        Usa la barra de búsqueda para localizar rápidamente un banco por nombre, número ABA o código SWIFT. La visibilidad de columnas se puede ajustar con el botón de columnas ( ⠿ ) en la barra de herramientas.
    </div>

</div>
</div>{{-- /x-data --}}
