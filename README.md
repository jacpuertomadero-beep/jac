# JAC Afiliados

Aplicacion web PHP 8 con PDO, PostgreSQL, MVC, jQuery, AJAX, AdminLTE 3, Bootstrap 4.6, SweetAlert2, Select2, DateRangePicker y DataTables.

## Configuracion

1. Cree la base de datos PostgreSQL:

```sql
CREATE DATABASE jac;
```

2. Importe el archivo `database/schema.sql`.

Si la tabla `afiliados` ya existe, aplique tambien las migraciones en orden:

- `database/migrations/001_add_fecha_afiliacion.sql`
- `database/migrations/002_add_estado_afiliacion.sql`
- `database/migrations/003_add_asambleas_asistencias.sql`
- `database/migrations/004_add_comunicaciones.sql`
- `database/migrations/005_add_organizacion_comunal.sql`

3. Ajuste las credenciales de conexion en `config/config.php`.

4. Abra la aplicacion desde XAMPP:

```text
http://localhost/jac/
```

## Usuario inicial

```text
Email: admin@jac.local
Contrasena: admin123
```

## Rutas principales

La clase `AppController` usa `switch ($_GET['ruta'])` para resolver las rutas:

- `index.php?ruta=login`
- `index.php?ruta=dashboard`
- `index.php?ruta=afiliados`
- `index.php?ruta=afiliadoListar`
- `index.php?ruta=afiliadoGuardar`
- `index.php?ruta=afiliadoEditar`
- `index.php?ruta=afiliadoEliminar`
- `index.php?ruta=asambleas`
- `index.php?ruta=comunicaciones`
- `index.php?ruta=comunicacionListar`
- `index.php?ruta=comunicacionGuardar`
- `index.php?ruta=comunicacionEditar`
- `index.php?ruta=organizacion`
- `index.php?ruta=organizacionObtener`
- `index.php?ruta=organizacionGuardar`
