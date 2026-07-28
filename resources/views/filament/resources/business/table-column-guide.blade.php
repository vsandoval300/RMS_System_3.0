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
        <button class="tg-lang-btn" @click="lang = 'en'"
            :style="lang === 'en' ? 'background:#41A2C3; color:#ffffff;' : ''">EN</button>
        <button class="tg-lang-btn" @click="lang = 'es'"
            :style="lang === 'es' ? 'background:#41A2C3; color:#ffffff;' : ''">ES</button>
    </div>
</div>

<div class="tg-wrap">

    {{-- ── Always visible ─────────────────────────────────────────── --}}
    <div class="tg-section-label" x-text="lang === 'en' ? 'Always visible' : 'Siempre visible'"></div>

    <div class="tg-row">
        <div class="tg-col-name">#</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Sequential display number for the current page. Recalculates when filters or sort order change — it does not represent a permanent record ID.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número de visualización secuencial para la página actual. Se recalcula al cambiar filtros u orden — no representa un ID permanente del registro.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Business Code</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Unique identifier assigned to each business contract. Serves as the primary reference across all operative documents, transactions, and reports.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Identificador único asignado a cada contrato de negocio. Sirve como referencia principal en todos los documentos operativos, transacciones y reportes.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Index</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Internal sequential number used for ordering. Assigned at creation time and stable throughout the record's lifetime.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Número secuencial interno para ordenamiento. Se asigna al momento de la creación y permanece estable durante toda la vida del registro.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Reinsurance Type</div>
        <div class="tg-col-desc" x-show="lang === 'en'">The reinsurance modality that governs how risk is shared — e.g., Facultative, Proportional Treaty, Non-Proportional Treaty.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">La modalidad de reaseguro que define cómo se comparte el riesgo — ej., Facultativo, Contrato Proporcional, Contrato No Proporcional.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Reinsurer</div>
        <div class="tg-col-desc" x-show="lang === 'en'">The reinsurance company that accepts the ceded risk under this contract.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">La compañía reaseguradora que acepta el riesgo cedido bajo este contrato.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Coverages</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Risk categories covered by the contract, shown as badge tags using their acronyms. Hover any badge to see the full coverage name.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Categorías de riesgo cubiertas por el contrato, mostradas como etiquetas con sus acrónimos. Pasa el cursor sobre cualquier etiqueta para ver el nombre completo de la cobertura.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Renewed From</div>
        <div class="tg-col-desc" x-show="lang === 'en'">If this business is a renewal, shows a clickable link to the original contract it was renewed from. Empty for first-generation contracts.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Si este negocio es una renovación, muestra un enlace al contrato original del que fue renovado. Vacío para contratos de primera generación.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Currency</div>
        <div class="tg-col-desc" x-show="lang === 'en'">The foreign settlement currency (FTS) in which premiums and transactions are originally denominated.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">La moneda de liquidación extranjera (FTS) en la que se denominan originalmente las primas y transacciones.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Approval</div>
        <div class="tg-col-desc" x-show="lang === 'en'">
            Current position in the internal approval workflow:<br>
            <strong>Draft</strong> → <strong>Pending Review</strong> → <strong>Approved</strong> or <strong>Needs Revision</strong>.<br>
            <strong>Cancelled</strong> marks contracts terminated before or during approval.
        </div>
        <div class="tg-col-desc" x-show="lang === 'es'">
            Posición actual en el flujo de aprobación interno:<br>
            <strong>Draft</strong> → <strong>Pending Review</strong> → <strong>Approved</strong> o <strong>Needs Revision</strong>.<br>
            <strong>Cancelled</strong> marca contratos terminados antes o durante la aprobación.
        </div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Lifecycle</div>
        <div class="tg-col-desc" x-show="lang === 'en'">
            Operational status derived from operative document dates:<br>
            <strong>On Hold</strong> · <strong>Pending</strong> · <strong>In Force</strong> · <strong>To Expire</strong> · <strong>Expired</strong> · <strong>Cancelled</strong>.<br>
            Displays <em>↻ Ready to renew</em> when within the 45-day renewal window around the latest expiration date.
        </div>
        <div class="tg-col-desc" x-show="lang === 'es'">
            Estado operativo derivado de las fechas de los documentos operativos:<br>
            <strong>On Hold</strong> · <strong>Pending</strong> · <strong>In Force</strong> · <strong>To Expire</strong> · <strong>Expired</strong> · <strong>Cancelled</strong>.<br>
            Muestra <em>↻ Ready to renew</em> cuando el contrato está dentro de la ventana de renovación de 45 días alrededor de su fecha de vencimiento más reciente.
        </div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Documents</div>
        <div class="tg-col-desc" x-show="lang === 'en'">Total count of operative documents linked to this business — includes the original policy, endorsements, and extension amendments.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Conteo total de documentos operativos vinculados a este negocio — incluye la póliza original, endosos y modificaciones de extensión.</div>
    </div>

    {{-- ── Hidden by default ───────────────────────────────────────── --}}
    <div class="tg-section-label" style="margin-top:8px;">
        <span x-text="lang === 'en' ? 'Hidden by default' : 'Ocultas por defecto'"></span>
        <span x-show="lang === 'en'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (toggle via column selector)</span>
        <span x-show="lang === 'es'" style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px;"> (activar desde el selector de columnas)</span>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Treaty <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">The reinsurance treaty this business is grouped under, if applicable. Businesses without a treaty are standalone facultative placements.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">El tratado de reaseguro bajo el cual se agrupa este negocio, si aplica. Los negocios sin tratado son colocaciones facultativas independientes.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Premium Type <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">
            How the contract premium is structured:<br>
            <strong>Fixed</strong> — agreed amount, no adjustment needed.<br>
            <strong>Estimated</strong> — provisional amount, adjusted when actuals are known.<br>
            <strong>Declared</strong> — reported periodically via bordereaux.
        </div>
        <div class="tg-col-desc" x-show="lang === 'es'">
            Cómo se estructura la prima del contrato:<br>
            <strong>Fixed</strong> — monto acordado, sin ajuste necesario.<br>
            <strong>Estimated</strong> — monto provisional, ajustado cuando se conocen los reales.<br>
            <strong>Declared</strong> — reportada periódicamente vía bordereau.
        </div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Source ID <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">External or legacy reference code from the originating system, used to cross-reference records with other platforms.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Código de referencia externo o legado del sistema de origen, utilizado para cruzar registros con otras plataformas.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Created By <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">The user who registered this business in the system.</div>
        <div class="tg-col-desc" x-show="lang === 'es'">El usuario que registró este negocio en el sistema.</div>
    </div>

    <div class="tg-row">
        <div class="tg-col-name">Created At <span class="tg-hidden-badge">hidden</span></div>
        <div class="tg-col-desc" x-show="lang === 'en'">Date and time the record was first entered into the system (UTC).</div>
        <div class="tg-col-desc" x-show="lang === 'es'">Fecha y hora en que el registro fue ingresado por primera vez al sistema (UTC).</div>
    </div>

    <div class="tg-note" x-show="lang === 'en'">
        Columns marked <strong>hidden</strong> can be shown or hidden via the column toggle button ( ⠿ ) in the table toolbar. Your selection is saved per session.
    </div>
    <div class="tg-note" x-show="lang === 'es'">
        Las columnas marcadas como <strong>hidden</strong> se pueden mostrar u ocultar mediante el botón de columnas ( ⠿ ) en la barra de herramientas de la tabla. Tu selección se guarda por sesión.
    </div>

</div>
</div>{{-- /x-data --}}
