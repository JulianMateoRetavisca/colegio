@extends('layouts.app')

@section('title', 'Notas de Estudiantes')

@section('content')
<div class="container py-4">
    <h3>Notas de Estudiantes</h3>

    <div id="controls" class="my-3">
        <!-- El select se renderiza por JS cuando el usuario tiene permiso -->
        <div id="studentSelectWrapper"></div>
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
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const selectWrapper = document.getElementById('studentSelectWrapper');
    const notasTableBody = document.querySelector('#notasTable tbody');

    // Cargar lista de estudiantes (el endpoint devolverá solo el propio estudiante si no tiene permiso)
    fetch('{{ route('notas.lista.estudiantes') }}')
        .then(r => r.json())
        .then(list => {
            if (!list || list.length === 0) return;

            // Si hay más de 1 estudiante, probablemente el usuario tiene permiso para seleccionar
            if (list.length > 1) {
                const label = document.createElement('label');
                label.textContent = 'Selecciona estudiante';
                const select = document.createElement('select');
                select.className = 'form-select w-50';
                select.id = 'studentSelect';
                list.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id; opt.textContent = s.name;
                    select.appendChild(opt);
                });
                select.addEventListener('change', () => loadNotas(select.value));
                selectWrapper.appendChild(label);
                selectWrapper.appendChild(select);

                // Cargar notas del primer estudiante por defecto
                loadNotas(list[0].id);
            } else {
                // Solo el propio estudiante — cargar sus notas
                loadNotas(list[0].id);
            }
        })
        .catch(err => console.error(err));

    function loadNotas(studentId) {
        notasTableBody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
        fetch('{{ url('/notas/estudiante') }}/' + studentId)
            .then(r => r.json())
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
