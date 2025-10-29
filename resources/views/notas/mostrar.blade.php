@extends('layouts.app')

@section('title', 'Notas de Estudiantes')

@section('content')
<div class="container py-4">
    <h3>Notas de Estudiantes</h3>

    <div id="controls" class="my-3">
        <!-- El select se renderiza por JS cuando el usuario tiene permiso -->
        <div id="studentSelectWrapper"></div>
        <div id="debugArea" style="margin-top:8px;color:#555;font-size:0.9rem;
            background:#f8f9fa;padding:8px;border-radius:4px;display:none;">Debug:</div>
    </div>

    <div id="notasTableWrapper">
        <table class="table table-striped" id="notasTable">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Periodo</th>
                    <th>Nota</th>
                    <th>Estudiante</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($notas) && count($notas) > 0)
                    @foreach($notas as $n)
                        <tr>
                            <td>{{ $n->materia_id ?? 'N/A' }}</td>
                            <td>{{ $n->periodo }}</td>
                            <td>{{ $n->nota }}</td>
                            <td>{{ $n->estudiante->name ?? '' }}</td>
                        </tr>
                    @endforeach
                @else
                    {{-- Se rellenará por JS si existe --}}
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
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

    // Lista de materias disponible (coincide con NotasController)
    const materias = {
        1: 'Matemáticas',
        2: 'Lenguaje',
        3: 'Ciencias'
    };

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
                    tr.innerHTML = `<td>${escapeHtml(n.materia_id || 'N/A')}</td><td>${escapeHtml(n.periodo)}</td><td>${escapeHtml(n.nota)}</td><td>${escapeHtml(n.estudiante?.name || '')}</td>`;
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
        data.forEach(n => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${escapeHtml(n.materia_id || 'N/A')}</td><td>${escapeHtml(n.periodo)}</td><td>${escapeHtml(n.nota)}</td><td>${escapeHtml(n.estudiante?.name || '')}</td>`;
            notasTableBody.appendChild(tr);
        });
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
            groups[key].forEach(n => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${escapeHtml(n.materia_id || 'N/A')}</td><td>${escapeHtml(n.periodo)}</td><td>${escapeHtml(n.nota)}</td><td>${escapeHtml(n.estudiante?.name || '')}</td>`;
                notasTableBody.appendChild(tr);
            });
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
});
</script>

@endsection
