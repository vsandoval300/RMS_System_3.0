<style>
.cg-wrap{font-family:inherit;font-size:13px;line-height:1.6;}
.cg-section-title{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:light-dark(#9ca3af,#6b7280);padding:14px 0 7px;border-bottom:1px solid light-dark(#e5e7eb,#374151);margin-bottom:10px;}
.cg-section-title:first-of-type{padding-top:0;}
.cg-col-list{list-style:none;padding:0;margin:0 0 4px;display:flex;flex-direction:column;gap:10px;}
.cg-col-item{display:flex;flex-direction:column;gap:2px;}
.cg-col-name{font-weight:700;font-size:12.5px;color:light-dark(#111827,#f9fafb);}
.cg-col-desc{color:light-dark(#6b7280,#9ca3af);font-size:12px;}
</style>

<div class="cg-wrap" x-data="{ lang: $store.cgLang ?? 'en' }">

    <div style="display:flex;justify-content:flex-end;margin-bottom:14px;">
        <div style="display:flex;border:1px solid light-dark(#d1d5db,#374151);border-radius:999px;overflow:hidden;">
            <button type="button" @click="lang='en'" :style="lang==='en'?'background:#41A2C3;color:#fff;':''" style="padding:3px 12px;font-size:11px;font-weight:700;border:none;cursor:pointer;background:transparent;color:light-dark(#6b7280,#9ca3af);transition:all .15s;">EN</button>
            <button type="button" @click="lang='es'" :style="lang==='es'?'background:#41A2C3;color:#fff;':''" style="padding:3px 12px;font-size:11px;font-weight:700;border:none;cursor:pointer;background:transparent;color:light-dark(#6b7280,#9ca3af);transition:all .15s;">ES</button>
        </div>
    </div>

    <div class="cg-section-title" x-text="lang==='en'?'Table Columns':'Columnas de la tabla'"></div>

    <ul class="cg-col-list">
        <li class="cg-col-item">
            <span class="cg-col-name">ID</span>
            <span class="cg-col-desc" x-show="lang==='en'">Unique numeric identifier for the region record.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Identificador numérico único del registro de región.</span>
        </li>
        <li class="cg-col-item">
            <span class="cg-col-name" x-text="lang==='en'?'Name':'Nombre'"></span>
            <span class="cg-col-desc" x-show="lang==='en'">Full name of the geographic macro-region (e.g., North America, Europe, Asia Pacific). Regions group countries at the highest geographic level.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Nombre completo de la macro-región geográfica (ej. América del Norte, Europa, Asia Pacífico). Las regiones agrupan países en el nivel geográfico más alto.</span>
        </li>
        <li class="cg-col-item">
            <span class="cg-col-name">Region Code</span>
            <span class="cg-col-desc" x-show="lang==='en'">Short alphanumeric code used to identify the region internally (e.g., NAM, EUR). Used as a reference key when linking subregions and countries.</span>
            <span class="cg-col-desc" x-show="lang==='es'">Código alfanumérico corto para identificar la región internamente (ej. NAM, EUR). Se usa como clave de referencia al vincular subregiones y países.</span>
        </li>
    </ul>

</div>
