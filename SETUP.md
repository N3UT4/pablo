# Backend ITZA TATTOO — qué falta configurar

El proyecto ya corre en PHP + MySQL con el patrón MVC (`app/controllers`, `app/models`,
`app/views`). Todo el flujo de citas, pagos, contacto, consentimiento y promociones
quedó probado de punta a punta con datos reales. Esto es lo que **tú** debes ajustar
antes de publicarlo:

## 1. Conexión a la base de datos
Edita las variables de entorno (o los valores por defecto en `config/config.php`):

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=itza_tattoo
DB_USER=tu_usuario
DB_PASSWORD=tu_password
```

Luego importa el esquema:

```bash
mysql --default-character-set=utf8mb4 -u tu_usuario -p < database.sql
```

⚠️ Importa siempre con `--default-character-set=utf8mb4` (o desde phpMyAdmin, que ya
usa utf8mb4 por defecto). Si importas con el cliente `mysql` sin esa bandera, las tildes
se guardan mal (es un tema del importador, no del proyecto).

## 2. Código de acceso de staff
`galeria.html` y `consentimiento.html` piden un código antes de mostrar el formulario.
Está definido en dos lugares que deben coincidir:
- Frontend: `STAFF_CODE` en `js/galeria.js` y `js/consentimiento.js`
- Backend: `STAFF_ACCESS_CODE` en `config/config.php` (o variable de entorno `STAFF_ACCESS_CODE`)

Por defecto ambos usan `ITZA-STAFF-2026`. Cámbialo por uno propio en los tres lugares.

## 3. Carpeta de subida de fotos
Las fotos que el staff sube desde `galeria.html` se guardan en `img/gallery/`.
En tu hosting, esa carpeta debe tener permisos de escritura (755 o 775 según el
proveedor). Si usas InfinityFree/cPanel, revisa los permisos de `img/` completa.

## 4. Datos que ya dejé precargados en `database.sql`
- **Tatuadores** (tabla `artists`): agregué los 4 que ya aparecían en los `<select>`
  de `abono.html` y `galeria.html`. Los nombres no coinciden entre esos dos formularios
  ("Itza — Blackwork" vs "Itzel R. — Blackwork"): decide cuál usar y ajústalo en el HTML.
- **Servicios** (tabla `services`): los 7 slugs ahora coinciden exactamente con las
  opciones de `abono.html` (blackwork, realismo, fine_line, color, cover_up, piercing,
  personalizado).
- **Promociones** (tabla `promotions`): los 4 cupones que estaban simulados en
  `promociones.js` (WELCOME20, ITZA2026, REFERIDOS, LOYALTY) ahora existen en la BD.
  Nota: la tabla solo maneja **descuento en porcentaje**, no montos fijos en pesos —
  "REFERIDOS" lo dejé en 10% como aproximación; si quieres montos fijos hay que agregar
  una columna a `promotions`.

## 5. Cosas que quedaron con lógica básica, sin rediseñar
- **Consentimiento**: el formulario no pide "cita" explícitamente, así que el backend
  busca al cliente por su documento y usa su cita más reciente sin firmar. Si el cliente
  no tiene cuenta + cita agendada, el formulario avisa con un mensaje claro.
- **Perfil** (`perfil.html`): no lo conecté al backend. Los campos de dirección
  (barrio, ciudad, código postal) no existen en la tabla `users`; si quieres guardarlos
  hay que agregar esas columnas primero.

## 6. Cómo probarlo localmente
Con XAMPP: copia la carpeta a `htdocs`, importa `database.sql` desde phpMyAdmin, abre
`http://localhost/pablo-main/index.php?action=home`.

Con el servidor embebido de PHP (rápido para probar sin Apache):
```bash
php -S localhost:8000 index.php
```
