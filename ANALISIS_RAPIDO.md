# Análisis Rápido - Checklist

1. **password_verify() en login / password_hash() en registro** → [OK]
   - `ModeloUsuarios.php:92` usa `password_verify()` en `login()`
   - `ModeloUsuarios.php:61` usa `password_hash()` en `register()`

2. **Rutas principales sin vistas faltantes** → [OK]
   - `home` → `inicio.php`
   - `login` → `ingresar.php`
   - `dashboard` → `cliente-citas.php`
   - `booking` → `cliente-agendar.php`
   - Todas existen en `app/views/pages/`

3. **Modelos ejecutan SQL sobre tablas `usuario` y `cita`** → [ERROR por corregir]
   - `ModeloUsuarios.php` usa tabla **`users`** (no `usuario`)
   - `ModeloCitas.php` usa tabla **`appointments`** (no `cita`)