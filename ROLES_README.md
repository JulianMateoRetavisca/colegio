# 🎭 Sistema de Gestión de Roles - Colegio

## 📋 Descripción

Sistema completo de gestión de roles para el colegio, con controlador en español y permisos granulares para diferentes tipos de usuarios.

## 👥 Roles del Sistema

El sistema incluye 7 roles predefinidos que NO pueden ser eliminados:

### 1. **Admin** 
- **Descripción**: Administrador del Sistema con acceso total
- **Credenciales por defecto**:
  - Email: `admin@colegio.edu.co`
  - Contraseña: `admin123`
- **Permisos**: Acceso total al sistema

### 2. **Rector**
- **Descripción**: Máxima autoridad del colegio
- **Credenciales por defecto**:
  - Email: `rector@colegio.edu.co`
  - Contraseña: `rector123`
- **Permisos**: Gestión completa excepto configuración del sistema

### 3. **CoordinadorDisciplina**
- **Descripción**: Encargado de disciplina y convivencia escolar
- **Permisos**: Gestión disciplinaria, asistencia y reportes

### 4. **CoordinadorAcademico**
- **Descripción**: Encargado de actividades académicas
- **Permisos**: Gestión académica, horarios, notas y materias

### 5. **Profesor**
- **Descripción**: Docente del colegio
- **Permisos**: Registro de notas, actividades y comunicación

### 6. **Estudiante**
- **Descripción**: Estudiante del colegio
- **Permisos**: Ver notas, horarios y actividades

### 7. **Acudiente**
- **Descripción**: Padre de familia o acudiente
- **Permisos**: Ver información del estudiante y comunicación

## 🔐 Permisos Disponibles

### Gestión de Usuarios
- `gestionar_usuarios` - Crear, editar y eliminar usuarios
- `ver_usuarios` - Ver lista de usuarios
- `asignar_roles` - Asignar roles a usuarios

### Gestión de Estudiantes
- `gestionar_estudiantes` - CRUD de estudiantes
- `ver_estudiantes` - Ver lista de estudiantes
- `matricular_estudiantes` - Realizar matrículas
- `ver_historial_academico` - Ver historial completo
- `gestionar_asistencia` - Registro de asistencia

### Gestión de Docentes
- `gestionar_docentes` - CRUD de docentes
- `ver_docentes` - Ver lista de docentes
- `asignar_materias` - Asignar materias a docentes

### Gestión Académica
- `registrar_notas` - Registrar calificaciones
- `ver_notas` - Ver calificaciones
- `aprobar_notas` - Aprobar calificaciones finales
- `crear_actividades` - Crear tareas y actividades
- `gestionar_horarios` - Gestión de horarios
- `gestionar_periodos` - Configurar períodos académicos

### Gestión Disciplinaria
- `gestionar_disciplina` - Registrar incidencias
- `ver_reportes_disciplinarios` - Ver reportes
- `aprobar_sanciones` - Aprobar sanciones
- `justificar_inasistencias` - Justificar ausencias

### Comunicación
- `enviar_comunicados` - Enviar comunicados
- `comunicarse_acudientes` - Chat con acudientes
- `comunicarse_docentes` - Chat con docentes
- `ver_comunicados` - Ver comunicados

### Reportes
- `ver_reportes_generales` - Reportes generales
- `ver_reportes_academicos` - Reportes académicos
- `ver_reportes_financieros` - Reportes financieros
- `generar_reportes` - Crear reportes personalizados
- `exportar_reportes` - Exportar a Excel/PDF

### Gestión Financiera
- `gestionar_pagos` - Gestión de pagos
- `ver_pagos` - Ver información de pagos
- `generar_recibos` - Generar recibos
- `configurar_pensiones` - Configurar valores

### Configuración
- `configurar_sistema` - Configuración general
- `gestionar_roles` - CRUD de roles
- `gestionar_permisos` - Asignar permisos
- `ver_logs_sistema` - Ver logs
- `hacer_respaldos` - Respaldos de datos

### Permisos Personales
- `ver_perfil_propio` - Ver perfil
- `editar_perfil_propio` - Editar perfil
- `cambiar_contrasena` - Cambiar contraseña
- `ver_notificaciones` - Ver notificaciones

## 🛣️ Rutas Disponibles

### Rutas Web
```php
GET    /roles                    # Listar todos los roles
GET    /roles/crear              # Formulario crear rol
POST   /roles                    # Guardar nuevo rol
GET    /roles/{rol}              # Ver detalles de rol
GET    /roles/{rol}/editar       # Formulario editar rol
PUT    /roles/{rol}              # Actualizar rol
DELETE /roles/{rol}              # Eliminar rol
```

### Rutas AJAX
```php
POST   /roles/asignar-rol        # Asignar rol a usuario
POST   /roles/remover-rol        # Remover rol de usuario
GET    /roles/usuarios-sin-rol   # Obtener usuarios sin rol
GET    /roles/roles-sistema      # Obtener roles del sistema
```

## 💻 Métodos del Controlador

### Métodos Web

#### `index()`
Muestra la lista de todos los roles con el conteo de usuarios asignados.

**Acceso**: Admin, Rector

**Retorna**: Vista `roles.index`

#### `crear()`
Muestra el formulario para crear un nuevo rol.

**Acceso**: Admin, Rector

**Retorna**: Vista `roles.crear`

#### `guardar(Request $request)`
Almacena un nuevo rol en la base de datos.

**Acceso**: Admin, Rector

**Validación**:
- `nombre`: Requerido, único, máx 255 caracteres
- `descripcion`: Requerido, máx 500 caracteres
- `permisos`: Array opcional

**Retorna**: Redirección a `roles.index`

#### `mostrar(RolesModel $rol)`
Muestra los detalles de un rol específico con sus usuarios asignados.

**Acceso**: Admin, Rector

**Retorna**: Vista `roles.mostrar`

#### `editar(RolesModel $rol)`
Muestra el formulario de edición de un rol.

**Acceso**: Admin, Rector

**Retorna**: Vista `roles.editar`

#### `actualizar(Request $request, RolesModel $rol)`
Actualiza un rol existente.

**Acceso**: Admin, Rector

**Nota**: Los roles del sistema solo pueden modificar sus permisos, no su nombre ni descripción.

**Retorna**: Redirección a `roles.mostrar`

#### `eliminar(RolesModel $rol)`
Elimina un rol personalizado.

**Acceso**: Admin, Rector

**Validaciones**:
- No se pueden eliminar roles del sistema
- No se pueden eliminar roles con usuarios asignados

**Retorna**: Redirección a `roles.index`

### Métodos AJAX

#### `asignarRol(Request $request)`
Asigna un rol a un usuario.

**Parámetros**:
- `usuario_id`: ID del usuario
- `rol_id`: ID del rol a asignar

**Retorna**: JSON con estado de éxito

#### `removerRol(Request $request)`
Remueve el rol de un usuario.

**Parámetros**:
- `usuario_id`: ID del usuario

**Retorna**: JSON con estado de éxito

#### `obtenerUsuariosSinRol()`
Obtiene la lista de usuarios sin rol asignado.

**Retorna**: JSON con array de usuarios

#### `obtenerRolesSistema()`
Obtiene la lista de roles del sistema.

**Retorna**: JSON con array de roles

### Métodos Auxiliares Privados

#### `usuarioPuedeGestionarRoles()`
Verifica si el usuario autenticado puede gestionar roles.

**Retorna**: `boolean`

#### `esRolDelSistema($nombreRol)`
Verifica si un rol es del sistema (no eliminable).

**Retorna**: `boolean`

#### `obtenerPermisosDisponibles()`
Retorna el array de todos los permisos disponibles.

**Retorna**: `array`

## 🔧 Uso del Controlador

### Ejemplo 1: Crear un rol personalizado

```php
// En la vista roles/crear.blade.php
<form method="POST" action="{{ route('roles.guardar') }}">
    @csrf
    <input type="text" name="nombre" required>
    <textarea name="descripcion" required></textarea>
    
    @foreach($permisosDisponibles as $permiso => $descripcion)
        <input type="checkbox" name="permisos[]" value="{{ $permiso }}">
        {{ $descripcion }}
    @endforeach
    
    <button type="submit">Crear Rol</button>
</form>
```

### Ejemplo 2: Asignar rol via AJAX

```javascript
// Asignar rol a usuario
fetch('/roles/asignar-rol', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        usuario_id: 5,
        rol_id: 3
    })
})
.then(response => response.json())
.then(data => {
    if(data.exito) {
        alert(data.mensaje);
    }
});
```

### Ejemplo 3: Verificar permisos en vistas

```blade
{{-- En cualquier vista Blade --}}
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp

@if($rol && $rol->tienePermiso('gestionar_estudiantes'))
    <a href="{{ route('estudiantes.crear') }}">Crear Estudiante</a>
@endif
```

### Ejemplo 4: Verificar permisos en controladores

```php
// En cualquier controlador
public function crearEstudiante()
{
    $usuario = Auth::user();
    $rol = RolesModel::find($usuario->roles_id);
    
    if (!$rol || !$rol->tienePermiso('gestionar_estudiantes')) {
        return redirect()->back()
            ->with('error', 'No tienes permisos para realizar esta acción.');
    }
    
    // Continuar con la lógica...
}
```

## 📦 Instalación y Configuración

### 1. Ejecutar migraciones
```bash
php artisan migrate
```

### 2. Ejecutar seeders
```bash
# Opción 1: Ejecutar todos los seeders
php artisan db:seed

# Opción 2: Ejecutar seeders específicos
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=UsuariosAdministradoresSeeder
```

### 3. Resetear base de datos (desarrollo)
```bash
php artisan migrate:fresh --seed
```

## 🔒 Seguridad

1. **Cambiar contraseñas por defecto** inmediatamente después de la instalación
2. Los roles del sistema están protegidos contra eliminación
3. Solo Admin y Rector pueden gestionar roles
4. Validación de permisos en cada operación
5. Protección CSRF en todas las rutas POST/PUT/DELETE

## 📝 Notas Importantes

- Los roles del sistema NO pueden ser eliminados
- Los roles del sistema solo pueden modificar sus permisos, no su nombre ni descripción
- No se puede eliminar un rol con usuarios asignados
- Todos los métodos del controlador están en español
- Todas las variables y objetos están en español
- Los mensajes de error y éxito están en español

## 🚀 Próximos Pasos

1. Crear las vistas Blade para cada método del controlador
2. Implementar middleware personalizado para verificación de permisos
3. Agregar logging para auditoría de cambios en roles
4. Implementar caché para optimizar consultas de permisos
5. Crear pruebas unitarias para el controlador

## 📞 Soporte

Para preguntas o problemas, contactar al administrador del sistema.

---

**Versión**: 1.0.0  
**Última actualización**: Octubre 2025
