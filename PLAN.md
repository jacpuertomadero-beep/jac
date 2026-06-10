# PLAN.md

## Analisis general

El proyecto es una aplicacion web PHP 8 para la gestion de una Junta de Accion Comunal. La estructura sigue un MVC sencillo sin Composer: un archivo frontal `index.php`, un controlador principal con `switch` por ruta, modelos que heredan de una clase de conexion PDO, vistas PHP separadas y recursos publicos en `public/assets`.

La aplicacion usa PostgreSQL como base de datos, sesiones PHP para autenticacion, jQuery/AJAX para operaciones asincronas y AdminLTE 3 como plantilla visual sobre Bootstrap 4.6.

## Estructura principal

```text
app/
  controllers/
    AppController.php
  core/
    Conexion.php
  models/
    Afiliado.php
    Asamblea.php
    Usuario.php
  views/
    afiliados/
      index.php
    asambleas/
      index.php
    layouts/
      main.php
    partials/
      footer.php
      header.php
      sidebar.php
    dashboard.php
    login.php
config/
  config.php
database/
  migrations/
  schema.sql
public/
  assets/
    css/
      app.css
    js/
      app.js
index.php
README.md
STRUCTURE.md
```

## Flujo de entrada

1. `index.php` inicia la sesion.
2. Carga configuracion, conexion, modelos y controlador.
3. Instancia `AppController`.
4. Ejecuta `dispatch()`.
5. `dispatch()` lee `$_GET['ruta']`.
6. El `switch` decide que metodo ejecutar.

Regla actual:

```php
$ruta = $_GET['ruta'] ?? 'dashboard';
```

Las rutas no usan un router externo. Toda ruta nueva debe agregarse manualmente en el `switch` de `AppController`.

## Regla MVC actual

### Controlador

`AppController.php` concentra:

- Resolucion de rutas.
- Validacion de formularios.
- Control de sesion.
- Respuestas JSON.
- Renderizado de vistas.
- Invocacion de modelos.

Regla actual: cada accion AJAX retorna JSON con estructura consistente:

```json
{ "ok": true, "message": "...", "data": {} }
```

Para DataTables, los endpoints deben retornar:

```json
{ "data": [] }
```

### Modelos

Los modelos extienden `Conexion`:

```php
class Afiliado extends Conexion
```

Regla actual:

- El modelo contiene consultas SQL.
- Usa PDO preparado cuando recibe datos externos.
- Usa transacciones cuando una operacion modifica varias tablas, como `Asamblea::guardar()`.

### Vistas

Las vistas son archivos PHP con HTML y variables inyectadas desde el controlador.

Regla actual:

- Las vistas privadas usan el layout `app/views/layouts/main.php`.
- Login usa una vista independiente.
- Los modulos principales tienen carpeta propia: `afiliados/`, `asambleas/`.

## Conexion a base de datos

La conexion vive en:

```text
app/core/Conexion.php
```

Reglas:

- Usa PDO con driver PostgreSQL.
- La configuracion esta en `config/config.php`.
- Los modelos deben llamar `$this->conectar()`.
- No se debe abrir conexion directamente desde vistas o JS.

## Base de datos

El esquema base esta en:

```text
database/schema.sql
```

Las migraciones incrementales estan en:

```text
database/migrations/
```

Tablas principales:

- `usuarios`: acceso administrador.
- `afiliados`: datos de afiliacion.
- `actas_asamblea`: registro de actas de asamblea.
- `asamblea_asistencias`: relacion entre actas y afiliados asistentes.

Reglas de integridad actuales:

- `usuarios.email` es unico.
- `afiliados.numero_afiliado` es unico.
- `afiliados.numero_identificacion` es unico.
- `afiliados.estado_afiliacion` solo admite `afiliado` o `desafiliado`.
- Si un afiliado esta `desafiliado`, debe tener acta/fallo y meses de sancion.
- `meses_sancion` debe estar entre 1 y 36.
- Cada afiliado solo puede aparecer una vez por acta en `asamblea_asistencias`.

## Modulo de autenticacion

Ruta principal:

```text
index.php?ruta=login
```

Reglas:

- El login se valida por AJAX.
- El password se verifica con `password_verify`.
- Los datos del usuario se guardan en `$_SESSION['usuario']`.
- Las rutas privadas llaman `requiereLogin()`.

## Modulo de afiliados

Rutas:

- `afiliados`
- `afiliadoListar`
- `afiliadoGuardar`
- `afiliadoEditar`
- `afiliadoEliminar`

Campos principales:

- Numero de afiliado.
- Fecha de afiliacion.
- Nombres completos.
- Edad.
- Numero de identificacion.
- Tipo de identificacion.
- Direccion.
- Comite de trabajo.
- Telefono.
- Estado de afiliacion.
- Acta o fallo del edicto.
- Meses de sancion.
- Observaciones.

Reglas:

- El formulario usa modal.
- El listado usa DataTables por AJAX.
- Fecha de afiliacion usa DateRangePicker en modo una sola fecha.
- Selects principales usan Select2.
- Si el estado es `desafiliado`, se activan campos obligatorios de sancion.
- La validacion critica tambien se repite en PHP.

## Modulo de asambleas

Rutas:

- `asambleas`
- `asambleaListar`
- `asambleaGuardar`
- `asambleaEditar`
- `asambleaEliminar`

Campos principales:

- Numero de acta.
- Fecha de asamblea.
- Afiliados asistentes.
- Observaciones.

Reglas:

- El listado usa DataTables.
- La fecha de asamblea usa DateRangePicker en modo una sola fecha.
- Los asistentes se relacionan en `asamblea_asistencias`.
- El porcentaje de participacion se calcula contra el total de afiliados registrados.

Formula actual:

```text
participacion = asistentes / total_afiliados * 100
```

## JavaScript

Archivo principal:

```text
public/assets/js/app.js
```

Reglas actuales:

- Usa jQuery.
- Los formularios se envian por AJAX.
- SweetAlert2 muestra confirmaciones y mensajes.
- DataTables carga datos por endpoints JSON.
- Select2 mejora selects simples y multiples.
- DateRangePicker administra fechas.

Convencion:

- Cada modulo se identifica por IDs especificos:
  - `tablaAfiliados`
  - `formAfiliado`
  - `tablaAsambleas`
  - `formAsamblea`

## CSS

Archivo principal:

```text
public/assets/css/app.css
```

Reglas actuales:

- La base visual es AdminLTE 3.
- Bootstrap 4.6 define grilla y componentes.
- `tool-panel` se usa como contenedor principal de modulo.
- Las tablas tienen `table-shell`.
- Las acciones de fila usan botones compactos.

## Convenciones para nuevas funcionalidades

1. Crear o actualizar tabla en `database/schema.sql`.
2. Crear migracion incremental en `database/migrations`.
3. Crear modelo en `app/models`.
4. Cargar el modelo en `index.php`.
5. Registrar rutas en el `switch` de `AppController`.
6. Crear vista en `app/views/{modulo}/index.php`.
7. Agregar opcion en `partials/sidebar.php` si es un modulo navegable.
8. Agregar comportamiento AJAX en `public/assets/js/app.js`.
9. Verificar sintaxis con:

```text
C:\xampp\php\php.exe -l archivo.php
```

10. Verificar endpoints AJAX con sesion activa.

## Riesgos actuales

- `AppController` esta creciendo y concentra demasiadas responsabilidades.
- No hay proteccion CSRF en formularios.
- Las credenciales de base de datos estan en archivo plano.
- `display_errors` esta activo; debe desactivarse en produccion.
- El JS central puede crecer demasiado si se agregan mas modulos.
- No hay pruebas automatizadas.

## Recomendaciones

1. Separar controladores por modulo cuando el proyecto crezca:
   - `AfiliadoController`
   - `AsambleaController`
   - `AuthController`

2. Crear una capa de helpers para:
   - Respuestas JSON.
   - Validacion.
   - Redireccion.
   - Renderizado.

3. Mover credenciales sensibles a variables de entorno.

4. Agregar token CSRF para operaciones POST.

5. Mantener migraciones en orden numerico.

6. Evitar consultas SQL desde controladores o vistas.

7. Mantener DataTables siempre con endpoints que respondan `{ "data": [] }`.

8. Separar JS por modulo si el archivo `app.js` sigue creciendo.

## Estado actual

La aplicacion ya tiene una base funcional para:

- Inicio de sesion administrador.
- CRUD de afiliados.
- Estado afiliado/desafiliado con reglas de sancion.
- Registro de actas de asamblea.
- Registro de asistentes por acta.
- Calculo de porcentaje de participacion.

La estructura es adecuada para una primera version MVC manual. El siguiente paso tecnico recomendado es modularizar controlador y JavaScript para reducir acoplamiento.
