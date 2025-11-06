<div id="taskbar-notas" aria-label="Barra notas" style="position:fixed; left:240px; top:120px; z-index:1020;">
    <style>
        #taskbar-notas { transition: transform .12s ease; }
        #taskbar-notas .btn-tn { display:flex; align-items:center; gap:10px; padding:10px 14px; background:#ffffffcc; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06); margin-bottom:12px; cursor:pointer; border:1px solid rgba(0,0,0,0.04); }
        #taskbar-notas .btn-tn i { width:20px; text-align:center; }
        #taskbar-notas .btn-tn:hover { transform:translateY(-2px); box-shadow:0 10px 24px rgba(0,0,0,0.10); }
        @media(max-width:992px){ #taskbar-notas{ display:none; } }
    </style>

    <div class="btn-tn" onclick="location.href='{{ route('horarios.index') }}'" title="Ir a Horarios">
        <i class="fas fa-calendar-alt"></i>
        <span class="d-none d-md-inline">Horarios</span>
    </div>

    <script>
        // no extra JS for now; kept simple to mirror notas quick actions
    </script>
</div>
