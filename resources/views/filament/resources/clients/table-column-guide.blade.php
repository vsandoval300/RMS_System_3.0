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

    <div class="tg-section-label" x-text="lang === 'en' ? 'Always visible' : 'Siempre visible'"></div>

    <div class="tg-row">
        <div class="tg-col-name">#</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Sequential display number for the current page. Recalculates when filters or sort order change — it does not represent a permanent record ID.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número de visualización secuencial para la página actual. Se recalcula al cambiar filtros u orden — no representa un ID permanente del registro.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Name</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Full legal name of the client organization. This is the primary display name used throughout the platform in contracts, reports, and operative documents.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre legal completo de la organización cliente. Es el nombre principal que se usa en toda la plataforma en contratos, reportes y documentos operativos.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Description</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Free-text field for additional context about the client — business sector, notes on the relationship, or any other relevant information not captured in structured fields.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Campo de texto libre para contexto adicional sobre el cliente — sector empresarial, notas sobre la relación, u otra información relevante no capturada en campos estructurados.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Country</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Country of incorporation or primary operations of the client. Used for geographic segmentation in reports and compliance checks.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">País de constitución u operaciones principales del cliente. Se usa para segmentación geográfica en reportes y verificaciones de cumplimiento.</div>
    </div>

    {{-- ── Hidden by default ───────────────────────────────────────── --}}
    <div class="tg-section-label" style="margin-top:8px;">
        <span x-text="lang === 'en' ? 'Hidden by default' : 'Ocultas por defecto'"></span>
        <span x-show="lang === 'en'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (toggle via column selector)</span>
        <span x-show="lang === 'es'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (activar desde el selector de columnas)</span>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Short Name <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">Abbreviated or trade name of the client — useful for identifying the organization quickly in dense views or reports where the full name is too long.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Nombre abreviado o comercial del cliente — útil para identificar rápidamente la organización en vistas densas o reportes donde el nombre completo es muy largo.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Web Page <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">URL of the client's official website. Useful for due diligence verification and maintaining up-to-date contact references.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">URL del sitio web oficial del cliente. Útil para verificaciones de due diligence y para mantener referencias de contacto actualizadas.</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">
        Columns marked <strong>hidden</strong> can be shown or hidden via the column toggle button ( ⠿ ) in the table toolbar. Your selection is saved per session.
    </div>
    <div class="tg-note" x-show="lang === 'es'">
        Las columnas marcadas como <strong>hidden</strong> se pueden mostrar u ocultar mediante el botón de columnas ( ⠿ ) en la barra de herramientas de la tabla. Tu selección se guarda por sesión.
    </div>

</div>
</div>{{-- /x-data --}}
