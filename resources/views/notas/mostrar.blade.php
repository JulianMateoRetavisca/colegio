@extends('layouts.app')

@section('title', 'Notas de Estudiantes')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-list-alt me-2 text-primary"></i>Notas de Estudiantes</h1>
            <p class="subtitle">Gestión de calificaciones y estados</p>
        </div>
        <div class="action-bar">
            <a href="{{ route('notas.guardar') }}" class="btn-pro primary"><i class="fas fa-plus me-1"></i>Nueva Nota</a>
                        @if(isset($isStudent) && $isStudent)
                        <button type="button" id="refreshNotasBtn" class="btn-pro outline" title="Actualizar listado"><i class="fas fa-sync"></i></button>
                        @endif
        </div>
    </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

    <div id="controls" class="my-3">
        <!-- El select se renderiza por JS cuando el usuario tiene permiso -->
        <div id="studentSelectWrapper"></div>
        @if(isset($isAdmin) && $isAdmin)
            <form method="GET" action="{{ route('notas.mostrar') }}" class="row g-2 align-items-end mb-3">
                <div>
                    <label class="form-label">Grupo</label>
                    <select name="grupo" class="form-select">
                        <option value="">Todos</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id }}" {{ (isset($selectedGrupo) && $selectedGrupo == $g->id) ? 'selected' : '' }}>{{ $g->nombre ?? 'Grupo '.$g->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Materia</label>
                    <select name="materia" class="form-select">
                        <option value="">Todas</option>
                        @foreach($materias as $mid => $mname)
                            <option value="{{ $mid }}" {{ (isset($selectedMateria) && $selectedMateria == $mid) ? 'selected' : '' }}>{{ $mname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn-pro primary">Filtrar</button>
                </div>
            </form>
            <script>window.serverControls = true;</script>
        @endif
        @if(isset($isStudent) && $isStudent)
            <div class="mb-2">
                <label class="form-label">Filtrar por materia</label>
                <select id="studentMateriaFilter" class="form-select form-select-sm" style="max-width:260px;">
                    <option value="">Todas</option>
                    @foreach($materias as $mid => $mname)
                        <option value="{{ $mid }}">{{ $mname }}</option>
                    @endforeach
                </select>
            </div>
            <script>window.studentControls = true;</script>
        @endif
        <div id="debugArea" style="margin-top:8px;color:#555;font-size:0.9rem;
            background:#f8f9fa;padding:8px;border-radius:4px;display:none;">Debug:</div>
    </div>

    <div id="notasTableWrapper" class="pro-card">
        <div class="pro-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="h6 mb-0">Listado de Notas</h2>
            <small class="text-muted">Estados: Borrador → Publicada → Revisada → Bloqueada</small>
        </div>
        <div class="pro-table-wrapper">
        <table class="pro-table" id="notasTable">
            <thead>
                <tr>
                    <th>Materia</th><th>Periodo</th><th>Nota</th><th>Estado</th><th>Estudiante</th><th style="width:240px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($notas) && count($notas) > 0)
                    @foreach($notas as $n)
                        <tr data-id="{{ $n->id }}">
                            <td>{{ isset($materias[$n->materia_id]) ? $materias[$n->materia_id] : ($n->materia_id ?? 'N/A') }}</td>
                            <td class="periodo">{{ $n->periodo }}</td>
                            <td class="nota">{{ $n->nota }}</td>
                            <td class="estado">
                                @php($estado = $n->estado ?? 'borrador')
                                @switch($estado)
                                    @case('borrador')<span class="badge bg-secondary">Borrador</span>@break
                                    @case('publicada')<span class="badge bg-info">Publicada</span>@break
                                    @case('revisada')<span class="badge bg-success">Revisada</span>@break
                                    @case('bloqueada')<span class="badge bg-dark">Bloqueada</span>@break
                                    @default <span class="badge bg-light text-dark">{{ $estado }}</span>
                                @endswitch
                            </td>
                            <td>{{ $n->estudiante->name ?? '' }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                @if(Auth::check() && Auth::user()->tienePermiso('modificar_notas'))
                                    <button class="btn-pro xs outline editNotaBtn" data-id="{{ $n->id }}" data-materia="{{ $n->materia_id }}" data-periodo="{{ $n->periodo }}" data-nota="{{ $n->nota }}" title="Editar"><i class="fas fa-edit"></i></button>
                                @endif
                                @if(Auth::check() && Auth::user()->tienePermiso('gestionar_notas'))
                                    @php($estado = $n->estado ?? 'borrador')
                                    @if($estado === 'borrador')
                                        <form method="POST" action="{{ route('notas.publicar',$n->id) }}" class="d-inline">@csrf <button class="btn-pro xs info" title="Publicar"><i class="fas fa-upload"></i></button></form>
                                    @elseif($estado === 'publicada')
                                        <form method="POST" action="{{ route('notas.revisar',$n->id) }}" class="d-inline">@csrf <button class="btn-pro xs success" title="Revisar"><i class="fas fa-check"></i></button></form>
                                        <form method="POST" action="{{ route('notas.revertir',$n->id) }}" class="d-inline">@csrf <button class="btn-pro xs warning" title="Revertir"><i class="fas fa-undo"></i></button></form>
                                    @elseif($estado === 'revisada')
                                        <form method="POST" action="{{ route('notas.revertir',$n->id) }}" class="d-inline">@csrf <button class="btn-pro xs warning" title="Revertir"><i class="fas fa-undo"></i></button></form>
                                        <form method="POST" action="{{ route('notas.bloquear',$n->id) }}" class="d-inline" onsubmit="return confirm('¿Bloquear nota definitivamente?');">@csrf <button class="btn-pro xs dark" title="Bloquear"><i class="fas fa-lock"></i></button></form>
                                    @endif
                                @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{-- Se rellenará por JS si existe --}}
                @endif
            </tbody>
        </table>
    </div>
    </div>
</section>

<script>
    // CSRF token for AJAX
    const csrfToken = '{{ csrf_token() }}';
    // permiso para modificar notas en JS
    window.canModify = {{ (Auth::check() && Auth::user()->tienePermiso('modificar_notas')) ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function(){
    // marcar que el script se inició
    try { showDebug('Script cargado'); } catch(e) { console.warn('showDebug aún no definido'); }
    // Capturar errores globales de JS y promesas no manejadas para mostrarlos en la UI
    window.addEventListener('error', function(ev){
        try { showDebug('JS Error: ' + ev.message + ' @ ' + ev.filename + ':' + ev.lineno); } catch(e) { console.error(ev); }
    });
    window.addEventListener('unhandledrejection', function(ev){
        try { showDebug('Unhandled Rejection: ' + (ev.reason && ev.reason.message ? ev.reason.message : JSON.stringify(ev.reason))); } catch(e) { console.error(ev); }
    });
    const selectWrapper = document.getElementById('studentSelectWrapper');
    const notasTableBody = document.querySelector('#notasTable tbody');
    const debugArea = document.getElementById('debugArea');

    function showDebug(msg) {
        if (!debugArea) return;
        debugArea.style.display = 'block';
        const ts = new Date().toISOString().replace('T',' ').split('.')[0];
        debugArea.textContent = ts + ' - ' + msg;
        console.debug('[notas.mostrar] ' + msg);
    }

    // Delegated handler for Edit buttons
    document.addEventListener('click', function(ev){
        if (!window.canModify) return;
        const btn = ev.target.closest && ev.target.closest('.editNotaBtn');
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        const currentNota = btn.getAttribute('data-nota');
        const currentPeriodo = btn.getAttribute('data-periodo');
        const newNota = prompt('Ingrese nueva nota (0-100):', currentNota);
        if (newNota === null) return; // cancel
        const newPeriodo = prompt('Ingrese periodo (ej. 2025):', currentPeriodo);
        if (newPeriodo === null) return;
        // enviar PUT
        const url = '{{ url('/notas') }}/' + id;
        showDebug('Actualizando nota ' + id + ' -> nota=' + newNota + ', periodo=' + newPeriodo);
        fetch(url, {
            method: 'PUT',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ nota: newNota, periodo: newPeriodo })
        })
        .then(r => {
            if (!r.ok) return r.text().then(t => { throw new Error('HTTP ' + r.status + ' - ' + t); });
            return r.json();
        })
        .then(data => {
            showDebug('Nota actualizada: ' + JSON.stringify(data));
            // actualizar fila en la tabla si existe
            const row = document.querySelector('tr[data-id="' + id + '"]');
            if (row) {
                const notaCell = row.querySelector('.nota');
                const periodoCell = row.querySelector('.periodo');
                if (notaCell) notaCell.textContent = data.nota;
                if (periodoCell) periodoCell.textContent = data.periodo;
                // actualizar data attributes
                btn.setAttribute('data-nota', data.nota);
                btn.setAttribute('data-periodo', data.periodo);
            } else {
                // si no hay fila, recargar la página para ver cambios
                location.reload();
            }
        })
        .catch(err => { console.error(err); showDebug('Error actualizando nota: ' + err.message); alert('Error: ' + err.message); });
    });

    // Lista de materias disponible (coincide con NotasController)
    const materias = {
        1: 'Matemáticas',
        2: 'Lenguaje',
        3: 'Ciencias'
    };

    // Si existen controles renderizados por servidor, evitar duplicar los selects con JS
    if (window.serverControls) {
        showDebug('Controles renderizados por servidor — JS no duplicará selects');
    } else if (window.studentControls) {
        showDebug('Controles de estudiante renderizados por servidor — JS no duplicará selects');
        // Si hay un control de materia para estudiante, enlazarlo a la búsqueda por usuario
        try {
            const stuSelect = document.getElementById('studentMateriaFilter');
            const uid = '{{ $currentUserId ?? '' }}';
            const triggerFetch = function(){
                const mid = (document.getElementById('studentMateriaFilter') || {}).value || '';
                const params = new URLSearchParams();
                if (mid) params.append('materia', mid);
                if (uid) params.append('usuario', uid);
                const url = '{{ route('notas.filtros') }}' + (params.toString() ? ('?' + params.toString()) : '');
                showDebug('Solicitando (estudiante): ' + url);
                notasTableBody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
                fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(data => renderNotas(data))
                    .catch(err => { console.error(err); showDebug('Error: ' + err.message); notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>'; });
            };
            if (stuSelect) {
                stuSelect.addEventListener('change', triggerFetch);
            }
            // Carga inicial automática de notas del estudiante autenticado
            triggerFetch();
        } catch(e) { console.error(e); }
    } else {
        // Primero intentar cargar grupos (si existen) y luego estudiantes por grupo.
        fetch('{{ route('notas.grupos') }}', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(grupos => {
            if (grupos && grupos.length > 0) {
                // Crear selector de grupos
                const gLabel = document.createElement('label');
                gLabel.textContent = 'Selecciona grupo';
                const gSelect = document.createElement('select');
                gSelect.className = 'form-select w-50 mb-2';
                gSelect.id = 'groupSelect';
                grupos.forEach(g => {
                    const opt = document.createElement('option');
                    opt.value = g.id; opt.textContent = g.nombre || ('Grupo ' + g.id);
                    gSelect.appendChild(opt);
                });
                // Opción para todos los grupos
                const optAllGroups = document.createElement('option');
                optAllGroups.value = ''; optAllGroups.textContent = 'Todos los grupos';
                gSelect.insertBefore(optAllGroups, gSelect.firstChild);
                selectWrapper.appendChild(gLabel);
                selectWrapper.appendChild(gSelect);

                // Crear selector de materia (filtro)
                const mLabel = document.createElement('label');
                mLabel.textContent = 'Filtrar por materia';
                mLabel.style.marginLeft = '12px';
                const mSelect = document.createElement('select');
                mSelect.className = 'form-select w-25 mb-2';
                mSelect.id = 'materiaSelect';
                const optAllM = document.createElement('option'); optAllM.value = ''; optAllM.textContent = 'Todas las materias'; mSelect.appendChild(optAllM);
                for (const id in materias) { const opt = document.createElement('option'); opt.value = id; opt.textContent = materias[id]; mSelect.appendChild(opt); }
                selectWrapper.appendChild(mLabel);
                selectWrapper.appendChild(mSelect);

                // Crear selector opcional de estudiante dentro del grupo
                const sLabel = document.createElement('label');
                sLabel.textContent = 'Seleccionar estudiante (opcional)';
                sLabel.style.marginLeft = '12px';
                const sSelect = document.createElement('select');
                sSelect.className = 'form-select w-50 mb-2';
                sSelect.id = 'studentInGroupSelect';
                const sOptAll = document.createElement('option'); sOptAll.value = ''; sOptAll.textContent = 'Todos los estudiantes';
                sSelect.appendChild(sOptAll);
                selectWrapper.appendChild(sLabel);
                selectWrapper.appendChild(sSelect);

                // Cuando cambie el grupo o la materia, solicitar notas filtradas por grupo/materia y renderizar agrupadas por materia
                function refreshGrupoNotas() {
                    const gid = gSelect.value; // puede ser '' para todos los grupos
                    const mid = mSelect.value; // puede ser '' para todas las materias
                    const usuario = (document.getElementById('studentInGroupSelect') || {}).value;
                                notasTableBody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
                                const params = new URLSearchParams();
                                if (gid) params.append('grupo', gid);
                                if (mid) params.append('materia', mid);
                    if (usuario) params.append('usuario', usuario);
                                const url = '{{ route('notas.filtros') }}' + (params.toString() ? ('?' + params.toString()) : '');
                                console.log('Requesting filtered notes:', url);
                                showDebug('Solicitando: ' + url);
                                fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                                    .then(r => {
                                        console.log('Response status:', r.status);
                                        showDebug('Status: ' + r.status);
                                        if (!r.ok) return r.text().then(t => { throw new Error('HTTP ' + r.status + ' - ' + t); });
                                        return r.json().then(json => ({ status: r.status, json }));
                                    })
                                    .then(resp => {
                                        console.log('Filtered notes payload:', resp.json);
                                        showDebug('Payload: ' + JSON.stringify(resp.json).slice(0, 2000));
                                        renderGroupedByMateria(resp.json, mid);
                                    })
                                    .catch(err => {
                                        console.error(err);
                                        showDebug('Error: ' + (err.message || err));
                                        notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas del grupo</td></tr>';
                                    });
                }
                gSelect.addEventListener('change', refreshGrupoNotas);
                mSelect.addEventListener('change', refreshGrupoNotas);

                // Cuando cambie el grupo, además de cargar notas, poblar el selector de estudiantes del grupo
                gSelect.addEventListener('change', function(){
                    const gid = gSelect.value;
                    // limpiar estudiantes
                    sSelect.innerHTML = '';
                    const sOptAll2 = document.createElement('option'); sOptAll2.value = ''; sOptAll2.textContent = 'Todos los estudiantes'; sSelect.appendChild(sOptAll2);
                    if (!gid) {
                        refreshGrupoNotas();
                        return;
                    }
                    fetch('{{ url('/notas/grupo') }}/' + gid + '/estudiantes', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                        .then(r => {
                            if (!r.ok) throw new Error('HTTP ' + r.status);
                            return r.json();
                        })
                        .then(list => {
                            if (!list || list.length === 0) return;
                            list.forEach(s => {
                                const opt = document.createElement('option'); opt.value = s.id; opt.textContent = s.name; sSelect.appendChild(opt);
                            });
                            // disparar recarga de notas con el estudiante seleccionado (o ninguno)
                            refreshGrupoNotas();
                        })
                        .catch(err => { console.error(err); showDebug('Error cargando estudiantes del grupo: ' + err.message); refreshGrupoNotas(); });
                });

                // Disparar la carga inicial del primer grupo
                if (gSelect.options.length > 0) {
                    gSelect.dispatchEvent(new Event('change'));
                }
            } else {
                // No hay grupos — usar la lista de estudiantes existente
                fetch('{{ route('notas.lista.estudiantes') }}', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(list => {
                        if (!list || list.length === 0) return;

                        if (list.length > 1) {
                            const label = document.createElement('label');
                            label.textContent = 'Selecciona estudiante';
                            const select = document.createElement('select');
                            select.className = 'form-select w-50';
                            select.id = 'studentSelect';
                            // Opción para ver todas las notas
                            const optAll = document.createElement('option');
                            optAll.value = 'all'; optAll.textContent = 'Todos';
                            select.appendChild(optAll);
                            list.forEach(s => {
                                const opt = document.createElement('option');
                                opt.value = s.id; opt.textContent = s.name;
                                select.appendChild(opt);
                            });

                            // Crear selector de materia también para la vista sin grupos
                            const mLabel2 = document.createElement('label');
                            mLabel2.textContent = 'Filtrar por materia';
                            mLabel2.style.marginLeft = '12px';
                            const mSelect2 = document.createElement('select');
                            mSelect2.className = 'form-select w-25 mb-2';
                            mSelect2.id = 'materiaSelectNoGroup';
                            const optAllM2 = document.createElement('option'); optAllM2.value = ''; optAllM2.textContent = 'Todas las materias'; mSelect2.appendChild(optAllM2);
                            for (const id in materias) { const opt = document.createElement('option'); opt.value = id; opt.textContent = materias[id]; mSelect2.appendChild(opt); }

                            function refreshNotasByUser() {
                                const uid = select.value === 'all' ? null : select.value;
                                const mid = mSelect2.value;
                                const params = new URLSearchParams();
                                if (uid) params.append('usuario', uid);
                                if (mid) params.append('materia', mid);
                                const url = '{{ route('notas.filtros') }}' + (params.toString() ? ('?' + params.toString()) : '');
                                showDebug('Solicitando: ' + url);
                                notasTableBody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
                                fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                                    .then(data => renderNotas(data))
                                    .catch(err => { console.error(err); showDebug('Error: ' + err.message); notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>'; });
                            }

                            select.addEventListener('change', refreshNotasByUser);
                            mSelect2.addEventListener('change', refreshNotasByUser);

                            selectWrapper.appendChild(label);
                            selectWrapper.appendChild(select);
                            selectWrapper.appendChild(mLabel2);
                            selectWrapper.appendChild(mSelect2);

                            // cargar por defecto: si 'Todos' existe, cargar todos
                            refreshNotasByUser();
                        } else {
                            // un solo estudiante: mostrar sus notas (sin selector de materia)
                            const onlyId = list[0].id;
                            // mostrar también el filtro de materia
                            const mLabel2 = document.createElement('label');
                            mLabel2.textContent = 'Filtrar por materia';
                            mLabel2.style.marginLeft = '12px';
                            const mSelect2 = document.createElement('select');
                            mSelect2.className = 'form-select w-25 mb-2';
                            mSelect2.id = 'materiaSelectNoGroup';
                            const optAllM2 = document.createElement('option'); optAllM2.value = ''; optAllM2.textContent = 'Todas las materias'; mSelect2.appendChild(optAllM2);
                            for (const id in materias) { const opt = document.createElement('option'); opt.value = id; opt.textContent = materias[id]; mSelect2.appendChild(opt); }
                            selectWrapper.appendChild(mLabel2);
                            selectWrapper.appendChild(mSelect2);
                            mSelect2.addEventListener('change', function(){
                                const mid = mSelect2.value; const params = new URLSearchParams(); if (mid) params.append('materia', mid); params.append('usuario', onlyId);
                                const url = '{{ route('notas.filtros') }}' + (params.toString() ? ('?' + params.toString()) : '');
                                notasTableBody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
                                fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                                    .then(data => renderNotas(data))
                                    .catch(err => { console.error(err); notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>'; });
                            });
                            // cargar por defecto
                            const params = new URLSearchParams(); params.append('usuario', onlyId); const url = '{{ route('notas.filtros') }}' + (params.toString() ? ('?' + params.toString()) : '');
                            fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                                .then(data => renderNotas(data))
                                .catch(err => { console.error(err); notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>'; });
                        }
                    })
                    .catch(err => console.error(err));
            }
        })
        .catch(err => {
            console.error(err);
            // En caso de error en grupos, intentar cargar estudiantes directamente
            fetch('{{ route('notas.lista.estudiantes') }}', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(list => {
                    if (!list || list.length === 0) return;
                    if (list.length > 1) {
                        const label = document.createElement('label');
                        label.textContent = 'Selecciona estudiante';
                        const select = document.createElement('select');
                        select.className = 'form-select w-50';
                        select.id = 'studentSelect';
                        // Opción para ver todas las notas
                        const optAll = document.createElement('option');
                        optAll.value = 'all'; optAll.textContent = 'Todos';
                        select.appendChild(optAll);
                        list.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.id; opt.textContent = s.name;
                            select.appendChild(opt);
                        });

                        // Crear selector de materia también para la vista sin grupos
                        const mLabel2 = document.createElement('label');
                        mLabel2.textContent = 'Filtrar por materia';
                        mLabel2.style.marginLeft = '12px';
                        const mSelect2 = document.createElement('select');
                        mSelect2.className = 'form-select w-25 mb-2';
                        mSelect2.id = 'materiaSelectNoGroup';
                        const optAllM2 = document.createElement('option'); optAllM2.value = ''; optAllM2.textContent = 'Todas las materias'; mSelect2.appendChild(optAllM2);
                        for (const id in materias) { const opt = document.createElement('option'); opt.value = id; opt.textContent = materias[id]; mSelect2.appendChild(opt); }

                        function refreshNotasByUser() {
                            const uid = select.value === 'all' ? null : select.value;
                            const mid = mSelect2.value;
                            const params = new URLSearchParams();
                            if (uid) params.append('usuario', uid);
                            if (mid) params.append('materia', mid);
                            const url = '{{ route('notas.filtros') }}' + (params.toString() ? ('?' + params.toString()) : '');
                            showDebug('Solicitando: ' + url);
                            notasTableBody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
                            fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                                .then(data => renderNotas(data))
                                .catch(err => { console.error(err); showDebug('Error: ' + err.message); notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>'; });
                        }

                        select.addEventListener('change', refreshNotasByUser);
                        mSelect2.addEventListener('change', refreshNotasByUser);

                        selectWrapper.appendChild(label);
                        selectWrapper.appendChild(select);
                        selectWrapper.appendChild(mLabel2);
                        selectWrapper.appendChild(mSelect2);

                        // cargar por defecto: si 'Todos' existe, cargar todos
                        refreshNotasByUser();
                    } else {
                        // un solo estudiante: mostrar sus notas (sin selector de materia)
                        const onlyId = list[0].id;
                        // mostrar también el filtro de materia
                        const mLabel2 = document.createElement('label');
                        mLabel2.textContent = 'Filtrar por materia';
                        mLabel2.style.marginLeft = '12px';
                        const mSelect2 = document.createElement('select');
                        mSelect2.className = 'form-select w-25 mb-2';
                        mSelect2.id = 'materiaSelectNoGroup';
                        const optAllM2 = document.createElement('option'); optAllM2.value = ''; optAllM2.textContent = 'Todas las materias'; mSelect2.appendChild(optAllM2);
                        for (const id in materias) { const opt = document.createElement('option'); opt.value = id; opt.textContent = materias[id]; mSelect2.appendChild(opt); }
                        selectWrapper.appendChild(mLabel2);
                        selectWrapper.appendChild(mSelect2);
                        mSelect2.addEventListener('change', function(){
                            const mid = mSelect2.value; const params = new URLSearchParams(); if (mid) params.append('materia', mid); params.append('usuario', onlyId);
                            const url = '{{ route('notas.filtros') }}' + (params.toString() ? ('?' + params.toString()) : '');
                            notasTableBody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
                            fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                                .then(data => renderNotas(data))
                                .catch(err => { console.error(err); notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>'; });
                        });
                        // cargar por defecto
                        const params = new URLSearchParams(); params.append('usuario', onlyId); const url = '{{ route('notas.filtros') }}' + (params.toString() ? ('?' + params.toString()) : '');
                        fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                            .then(data => renderNotas(data))
                            .catch(err => { console.error(err); notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>'; });
                    }
                })
                .catch(err2 => console.error(err2));
        });

    function loadNotas(studentId) {
        notasTableBody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
        // Special values: 'all' -> todas las notas; otherwise -> notas por estudiante
        if (studentId === 'all') {
            fetch('{{ route('notas.index') }}', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(data => renderNotas(data))
                .catch(err => {
                    console.error(err);
                    notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>';
                });
            return;
        } else {
            fetch('{{ url('/notas/estudiante') }}/' + studentId, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(data => {
                notasTableBody.innerHTML = '';
                if (!data || data.length === 0) {
                    notasTableBody.innerHTML = '<tr><td colspan="4">No hay notas registradas</td></tr>';
                    return;
                }
                data.forEach(n => {
                    const tr = document.createElement('tr');
                    const matName = materias[n.materia_id] || n.materia_id || 'N/A';
                    tr.innerHTML = `<td>${escapeHtml(matName)}</td><td>${escapeHtml(n.periodo)}</td><td>${escapeHtml(n.nota)}</td><td>${escapeHtml(n.estudiante?.name || '')}</td>`;
                    notasTableBody.appendChild(tr);
                });
            })
            .catch(err => {
                console.error(err);
                notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>';
            });
    }

    function loadNotasGroup(grupoId) {
        notasTableBody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
        fetch('{{ url('/notas/grupo') }}/' + grupoId, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => renderGroupedByMateria(data))
            .catch(err => {
                console.error(err);
                notasTableBody.innerHTML = '<tr><td colspan="4">Error al cargar notas</td></tr>';
            });
    }

    function renderNotas(data) {
        notasTableBody.innerHTML = '';
        if (!data || data.length === 0) {
            notasTableBody.innerHTML = '<tr><td colspan="4">No hay notas registradas</td></tr>';
            return;
        }
        let sum = 0, count = 0;
        data.forEach(n => {
            const tr = document.createElement('tr');
            const estado = n.estado || 'borrador';
            tr.innerHTML = `<td>${escapeHtml(n.materia_id || 'N/A')}</td>
                            <td>${escapeHtml(n.periodo)}</td>
                            <td>${escapeHtml(n.nota)}</td>
                            <td>${escapeHtml(estado)}</td>
                            <td>${escapeHtml(n.estudiante?.name || '')}</td>`;
            notasTableBody.appendChild(tr);
            if (!isNaN(parseFloat(n.nota))) { sum += parseFloat(n.nota); count++; }
        });
        if (count > 0) {
          const avg = (sum / count).toFixed(2);
          const avgRow = document.createElement('tr');
          avgRow.innerHTML = `<td colspan="2" class="fw-bold">Promedio</td><td class="fw-bold">${avg}</td><td colspan="2"></td>`;
          notasTableBody.appendChild(avgRow);
        }
    }

    function renderGroupedByMateria(notes, materiaFilter) {
        notasTableBody.innerHTML = '';
        if (!notes || notes.length === 0) {
            notasTableBody.innerHTML = '<tr><td colspan="4">No hay notas registradas</td></tr>';
            return;
        }
        // Agrupar por materia_id
        const groups = {};
        notes.forEach(n => {
            const mid = n.materia_id || 'sin_materia';
            if (!groups[mid]) groups[mid] = [];
            groups[mid].push(n);
        });

        // Ordenar keys para consistencia
        const keys = Object.keys(groups).sort();
        keys.forEach(key => {
            if (materiaFilter && String(key) !== String(materiaFilter)) return;
            const matName = materias[key] || ('Materia ' + key);
            // Header row for materia
            const th = document.createElement('tr');
            th.innerHTML = `<td colspan="4" class="fw-bold bg-light">${escapeHtml(matName)}</td>`;
            notasTableBody.appendChild(th);
            // Rows for notes in this materia
            let sum = 0, count = 0;
            groups[key].forEach(n => {
                const tr = document.createElement('tr');
                const matName = materias[n.materia_id] || n.materia_id || 'N/A';
                tr.innerHTML = `<td>${escapeHtml(matName)}</td><td>${escapeHtml(n.periodo)}</td><td>${escapeHtml(n.nota)}</td><td>${escapeHtml(n.estudiante?.name || '')}</td>`;
                notasTableBody.appendChild(tr);
                if (!isNaN(parseFloat(n.nota))) { sum += parseFloat(n.nota); count++; }
            });
            if (count > 0) {
              const avg = (sum / count).toFixed(2);
              const avgRow = document.createElement('tr');
              avgRow.innerHTML = `<td colspan="2" class="text-end">Promedio ${escapeHtml(matName)}</td><td class="fw-bold">${avg}</td><td></td>`;
              notasTableBody.appendChild(avgRow);
            }
        });
    }

    function escapeHtml(unsafe) {
        return String(unsafe)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Botón manual de refresco (para estudiantes)
    const refreshBtn = document.getElementById('refreshNotasBtn');
    if (refreshBtn && window.studentControls) {
        refreshBtn.addEventListener('click', function(){
            const stuSelect = document.getElementById('studentMateriaFilter');
            if (stuSelect) stuSelect.dispatchEvent(new Event('change'));
        });
    }
});
</script>

@endsection
