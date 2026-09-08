# ITZA TATTOO STUDIO

Proyecto web de estudio de tatuajes con estructura MVC en PHP.

## Estructura

- app/controllers: controladores
- app/models: modelos
- app/views: vistas
- config: configuración
- core: base MVC
- public: se puede usar para una capa pública si luego se expande
- database.sql: estructura completa de MySQL

## Ejecutar localmente

1. Inicia Apache y MySQL desde XAMPP.
2. Ejecuta `database.sql` desde MySQL Workbench.
3. Configura las credenciales en `config/config.php`.
4. Abre:

```text
http://localhost:8080/itza-project/index.php?action=home
```

## GitHub

```bash
git init
git add .
git commit -m "Inicial MVC PHP"
git branch -M main
git remote add origin https://github.com/tu-usuario/tu-repositorio.git
git push -u origin main
```

## Base de datos

El registro y el inicio de sesión utilizan MySQL mediante PDO. La base de datos
esperada es `itza_tattoo`. El script incluye estas tablas:

- `users`: cuentas de clientes y usuarios del sistema.
- `artists`: tatuadores disponibles para las citas.
- `services`: puntillismo, blackwork, color, geometría, blackout y diseños personalizados.
- `appointments`: citas con fecha, hora, servicio, artista y estado.
- `payments`: abonos, comprobantes y estado de verificación.
- `consents`: consentimiento informado y datos del acudiente.
- `contact_messages`: mensajes recibidos desde contacto.
- `gallery`: fotografías de trabajos.
- `promotions` y `promotion_redemptions`: promociones y su uso.

Todas las tablas usan claves foráneas e índices básicos. Antes de importar el
script en una instalación que ya tenga datos, realiza un respaldo de la base.
El script usa `CREATE TABLE IF NOT EXISTS` y no elimina usuarios existentes.
No se utiliza almacenamiento JSON para la autenticación.

## Información del estudio

- Ubicación: Bogotá, La Victoria - 20 de Julio.
- Dirección: Calle 42 A Sur # 3C - 65 Este.
- WhatsApp: 301 400 3006.
- Horarios: 9:00 a. m. - 11:00 a. m.; 1:00 p. m. - 4:00 p. m.; 5:00 p. m. - 9:00 p. m.

## Publicar en InfinityFree

1. Crea una cuenta y un subdominio en InfinityFree.
2. En el panel crea una base MySQL y guarda el host, nombre, usuario y contraseña.
3. Importa el contenido de `database.sql` desde phpMyAdmin del hosting.
4. Sube el contenido del proyecto a `htdocs` usando el administrador de archivos o FTP.
5. Edita `config/config.php` con las credenciales de InfinityFree:

```php
define('DB_HOST', 'host-de-la-base');
define('DB_NAME', 'nombre-de-la-base');
define('DB_USER', 'usuario-de-la-base');
define('DB_PASSWORD', 'contraseña-de-la-base');
```

6. Abre la URL del subdominio. No uses `localhost` en producción.

GitHub sirve para almacenar el código, pero GitHub Pages no ejecuta PHP ni MySQL.
