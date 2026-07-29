<div x-data="{ lang: 'en' }">
<style>
.cg-wrap { font-family:inherit; font-size:13px; line-height:1.6; }
.cg-lang-toggle { display:flex; justify-content:flex-end; margin-bottom:16px; }
.cg-lang-btn-wrap { display:flex; border:1px solid light-dark(#d1d5db,#374151); border-radius:999px; overflow:hidden; }
.cg-lang-btn { padding:4px 12px; font-size:11px; font-weight:700; letter-spacing:0.05em; border:none; cursor:pointer; transition:all .15s; background:transparent; color:light-dark(#6b7280,#9ca3af); }
.cg-section-title { font-size:10px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:light-dark(#9ca3af,#6b7280); padding:16px 0 8px; border-bottom:1px solid light-dark(#e5e7eb,#374151); margin-bottom:10px; }
.cg-section-title:first-of-type { padding-top:0; }
.cg-para { color:light-dark(#4b5563,#9ca3af); margin-bottom:10px; }
.cg-status-list { list-style:none; padding:0; margin:0 0 10px; display:flex; flex-direction:column; gap:8px; }
.cg-status-item { display:flex; align-items:center; gap:10px; }
.cg-status-dot { width:13px; height:13px; border-radius:3px; flex-shrink:0; }
.cg-status-name { font-weight:600; font-size:12.5px; color:light-dark(#1f2937,#e5e7eb); min-width:90px; }
.cg-status-desc { color:light-dark(#6b7280,#9ca3af); font-size:12px; }
.cg-tip { font-size:12.5px; color:light-dark(#6b7280,#9ca3af); padding:10px 12px; background:light-dark(#f9fafb,#111827); border-left:3px solid #41A2C3; border-radius:0 4px 4px 0; margin-top:4px; }
.cg-questions { margin-top:4px; padding:0; list-style:none; display:flex; flex-direction:column; gap:6px; }
.cg-questions li { display:flex; gap:8px; font-size:12.5px; color:light-dark(#4b5563,#9ca3af); }
.cg-questions li::before { content:"→"; color:#41A2C3; flex-shrink:0; font-weight:700; }
</style>

<div class="cg-lang-toggle">
    <div class="cg-lang-btn-wrap">
        <button type="button" class="cg-lang-btn" @click="lang = 'en'" :style="lang === 'en' ? 'background:#41A2C3; color:#ffffff;' : ''">EN</button>
        <button type="button" class="cg-lang-btn" @click="lang = 'es'" :style="lang === 'es' ? 'background:#41A2C3; color:#ffffff;' : ''">ES</button>
    </div>
</div>

<div class="cg-wrap">

    {{-- What this chart shows --}}
    <div class="cg-section-title" x-text="lang === 'en' ? 'What this chart shows' : 'Qué muestra este gráfico'"></div>

    <div x-show="lang === 'en'" class="cg-para">
        A <strong>stacked bar chart</strong> that groups all business contracts by the year extracted from their Business Code, and breaks down each year by the current lifecycle status of each contract. The height of each colored segment represents the count of businesses in that status for that year.
    </div>
    <div x-show="lang === 'es'" class="cg-para">
        Un <strong>gráfico de barras apiladas</strong> que agrupa todos los contratos de negocio por el año extraído de su Código de Negocio, y desglosa cada año según el estado de ciclo de vida actual de cada contrato. La altura de cada segmento de color representa la cantidad de negocios en ese estado para ese año.
    </div>

    {{-- How to read it --}}
    <div class="cg-section-title" x-text="lang === 'en' ? 'How to read it' : 'Cómo leerlo'"></div>

    <div x-show="lang === 'en'" class="cg-para">
        Each bar on the X-axis represents a <strong>year</strong> (the first 4 characters of the Business Code). The total height of the bar is the total number of businesses registered in that year. The number shown above the bar is that total. Each color segment within the bar shows how many contracts are currently in each lifecycle state.
    </div>
    <div x-show="lang === 'es'" class="cg-para">
        Cada barra en el eje X representa un <strong>año</strong> (los primeros 4 caracteres del Código de Negocio). La altura total de la barra es el total de negocios registrados en ese año. El número sobre la barra es ese total. Cada segmento de color dentro de la barra muestra cuántos contratos están actualmente en cada estado de ciclo de vida.
    </div>

    {{-- Lifecycle status colors --}}
    <div class="cg-section-title" x-text="lang === 'en' ? 'Lifecycle status colors' : 'Colores de estado de ciclo de vida'"></div>

    <ul class="cg-status-list">
        <li class="cg-status-item">
            <span class="cg-status-dot" style="background:#FFF1D0; border:1px solid #e5d9a0;"></span>
            <span class="cg-status-name">On Hold</span>
            <span class="cg-status-desc" x-show="lang === 'en'">Contract is paused — not yet active or temporarily suspended.</span>
            <span class="cg-status-desc" x-show="lang === 'es'">Contrato pausado — aún no activo o suspendido temporalmente.</span>
        </li>
        <li class="cg-status-item">
            <span class="cg-status-dot" style="background:#06AED5;"></span>
            <span class="cg-status-name">In Force</span>
            <span class="cg-status-desc" x-show="lang === 'en'">Contract is currently active and covering risk.</span>
            <span class="cg-status-desc" x-show="lang === 'es'">Contrato actualmente vigente y cubriendo riesgo.</span>
        </li>
        <li class="cg-status-item">
            <span class="cg-status-dot" style="background:#F0C808;"></span>
            <span class="cg-status-name">To Expire</span>
            <span class="cg-status-desc" x-show="lang === 'en'">Contract is approaching its expiration date and may be eligible for renewal.</span>
            <span class="cg-status-desc" x-show="lang === 'es'">Contrato próximo a vencer y puede ser elegible para renovación.</span>
        </li>
        <li class="cg-status-item">
            <span class="cg-status-dot" style="background:#086788;"></span>
            <span class="cg-status-name">Expired</span>
            <span class="cg-status-desc" x-show="lang === 'en'">Contract has passed its expiration date and is no longer in force.</span>
            <span class="cg-status-desc" x-show="lang === 'es'">Contrato que ha superado su fecha de vencimiento y ya no está vigente.</span>
        </li>
        <li class="cg-status-item">
            <span class="cg-status-dot" style="background:#DD1C1A;"></span>
            <span class="cg-status-name">Cancelled</span>
            <span class="cg-status-desc" x-show="lang === 'en'">Contract was terminated before its expiration date.</span>
            <span class="cg-status-desc" x-show="lang === 'es'">Contrato terminado antes de su fecha de vencimiento.</span>
        </li>
    </ul>

    {{-- Questions it helps answer --}}
    <div class="cg-section-title" x-text="lang === 'en' ? 'Questions it helps answer' : 'Preguntas que ayuda a responder'"></div>

    <ul x-show="lang === 'en'" class="cg-questions">
        <li>Which years had the highest volume of new businesses?</li>
        <li>How many contracts from past years are still In Force today?</li>
        <li>What proportion of a given year's contracts have expired or been cancelled?</li>
        <li>Is the portfolio growing, stable, or contracting year over year?</li>
        <li>Which years have the most contracts pending renewal (To Expire)?</li>
    </ul>
    <ul x-show="lang === 'es'" class="cg-questions">
        <li>¿Qué años tuvieron el mayor volumen de nuevos negocios?</li>
        <li>¿Cuántos contratos de años anteriores siguen Vigentes hoy?</li>
        <li>¿Qué proporción de los contratos de un año han vencido o sido cancelados?</li>
        <li>¿El portafolio está creciendo, estable o contrayéndose año con año?</li>
        <li>¿Qué años tienen más contratos pendientes de renovación (Por vencer)?</li>
    </ul>

    <div class="cg-tip" x-show="lang === 'en'">The year in each bar comes from the first 4 characters of the <strong>Business Code</strong> (e.g. <em>2025</em>-ENE053-001), not from any date field. This reflects when the business was originally registered in the system.</div>
    <div class="cg-tip" x-show="lang === 'es'">El año de cada barra proviene de los primeros 4 caracteres del <strong>Código de Negocio</strong> (ej. <em>2025</em>-ENE053-001), no de ningún campo de fecha. Esto refleja cuándo fue registrado originalmente el negocio en el sistema.</div>

</div>
</div>
