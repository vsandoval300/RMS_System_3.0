<div style="display:flex; align-items:center; gap:10px; padding:10px 16px; border-top:1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));">
    <div style="line-height:1.4; flex-shrink:0;">
        <div style="font-size:12px; font-weight:700; color:light-dark(#374151,#d1d5db);">
            Version {{ config('app.version') }}
        </div>
        <div style="font-size:11px; color:light-dark(#9ca3af,#6b7280);">
            Build {{ config('app.build') }}
        </div>
    </div>
    <div style="width:1px; height:28px; background:light-dark(#d1d5db,#374151); flex-shrink:0;"></div>
    <div style="font-size:12px; color:light-dark(#9ca3af,#6b7280); line-height:1.3;">
        Powered by <span style="font-weight:600; color:light-dark(#6b7280,#9ca3af);">Rainmaker Group</span>
    </div>
</div>
