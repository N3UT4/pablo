# Reporte de Auditoría — Capa de Base de Datos y Modelos
## Proyecto: ITZA Tattoo Studio
## Fecha: 2026-09-21

---

## 1. HALLAZGOS DE AUDITORÍA

### 1.1 Esquemas de Base de Datos Conflictivos
| Archivo | Estado | Detalle |
|---------|--------|---------|
| `database/database.sql` | **CANÓNICO** | Esquema usado por el código actual. Tablas: `usuarios`, `artistas`, `servicios`, `citas`, `abonos`, `consentimientos`, `mensajes`, `galeria`, `horarios_artistas`, `promotions`, `canjes`. Incluye 2 stored procedures. |
| `database/ITZA TATTOO_bd.sql` | **LEGADO** | Esquema antiguo con nombres distintos: `usuario`, `cliente`, `cita`, `tatuador`, `abono`, `pago`. Incluye triggers y 4 stored procedures. **No coincide con el código actual.** |

**Acción requerida:** Estandarizar a un solo esquema. Los archivos refactorizados generados aquí se basan en `database.sql` (el canónico).

---

### 1.2 Modelos Actuales (sin Eloquent)
Los modelos existentes (`ModeloCitas`, `ModeloPagos`, `ModeloServicios`, `ModeloUsuarios`) extienden `ModeloBase` (PDO crudo). **No existen relaciones `hasMany` ni `belongsTo`** — las consultas JOIN se escriben manualmente en cada método.

### 1.3 Transacciones — NO HAY
El método `ControladorCitas::store()` (línea 90-105) ejecuta:
1. `$this->appointmentModel->create(...)` — INSERT INTO citas
2. `$this->paymentModel->create(...)` — INSERT INTO abonos

**Sin `DB::transaction`**: si el paso 2 falla, la cita queda creada sin pago asociado. **Violación de integridad financiera.**

### 1.4 Inyección SQL Potencial
`ModeloCitas::getLatestCitas()` (línea 285) y `getTransacciones()` (línea 303) usan `LIMIT $limit` con variable PHP interpolada directamente en el SQL. Aunque `$limit` siempre es `int`, es práctica riesgosa.

---

## 2. ARCHIVOS GENERADOS (REFACTORIZADOS)

### 2.1 Migraciones (`database/migrations/`)
| Archivo | Tabla | Relaciones definidas |
|---------|-------|---------------------|
| `2026_09_21_000001_create_clientes_table.php` | `clientes` | FK `usuario_id` → `usuarios.id` (CASCADE, UNIQUE) |
| `2026_09_21_000002_create_servicios_table.php` | `servicios` | Sin FK externas |
| `2026_09_21_000003_create_citas_table.php` | `citas` | FK `usuario_id` → `usuarios.id` (CASCADE), FK `artista_id` → `artistas.id`, FK `servicio_id` → `servicios.id` |
| `2026_09_21_000004_create_pagos_table.php` | `pagos` | FK `cita_id` → `citas.id` (CASCADE) |

Todas las migraciones incluyen:
- `up()` con `Schema::create`, columnas con tipos estrictos, FK constraints, índices
- `down()` con `Schema::dropIfExists`
- Índices compuestos para consultas frecuentes (fecha/hora, estado)

### 2.2 Modelos Eloquent (`app/models/`)
| Archivo | Tabla | Relaciones |
|---------|-------|------------|
| `Cliente.php` | `clientes` | `usuario()` → BelongsTo Usuario, `citas()` → HasMany Cita |
| `Servicio.php` | `servicios` | `citas()` → HasMany Cita |
| `Cita.php` | `citas` | `cliente()` → BelongsTo Cliente, `artista()` → BelongsTo Artista, `servicio()` → BelongsTo Servicio, `pagos()` → HasMany Pago |
| `Pago.php` | `pagos` | `cita()` → BelongsTo Cita, `crearConTransaccion()` → DB::transaction |
| `Usuario.php` | `usuarios` | Modelo stub referenciado por Cliente |
| `Artista.php` | `artistas` | Modelo stub referenciado por Cita |

### 2.3 Servicio Transaccional (`app/Services/BookingService.php`)
- `agendarYpagar(array $datos): array` — Ejecuta la creación de cita + pago dentro de una única `DB::transaction()`
- Validación previa fuera de la transacción (mensajes de error claros sin impacto en BD)
- Si cualquier paso falla: **ROLLBACK automático** de ambas operaciones

### 2.4 Método de Pago con Transacción (`Pago::crearConTransaccion()`)
Ubicado en `Pago.php`. Implementa:
1. Validación de campos obligatorios
2. `DB::transaction()` con closure
3. Verificación de existencia de la cita dentro de la transacción
4. Creación del pago dentro de la transacción
5. COMMIT automático o ROLLBACK ante cualquier excepción

---

## 3. RELACIONES ELOQUENT DEFINIDAS

```
Cliente --hasMany--> Cita
  (clientes.id --usuario_id--> citas.usuario_id)

Servicio --hasMany--> Cita
  (servicios.id --servicio_id--> citas.servicio_id)

Cita --belongsTo--> Cliente
Cita --belongsTo--> Artista
Cita --belongsTo--> Servicio
Cita --hasMany--> Pago
  (citas.id --cita_id--> pagos.cita_id)

Pago --belongsTo--> Cita
  (pagos.cita_id --id--> citas.id)
```

---

## 4. VERIFICACIÓN DE SINTAXIS

Los 11 archivos PHP generados fueron verificados con `php -l` (lint):
- ✅ 4 migraciones
- ✅ 6 modelos Eloquent (Cliente, Servicio, Cita, Pago, Usuario, Artista)
- ✅ 1 servicio (BookingService)

**Resultado: 0 errores de sintaxis en todos los archivos.**

---

## 5. RESUMEN DE MEJORAS SOBRE EL CÓDIGO ORIGINAL

| Aspecto | Original | Refactorizado |
|---------|----------|---------------|
| Tipo de modelo | PDO crudo (ModeloBase) | Eloquent Model |
| Relaciones | Ninguna (JOINs manuales) | hasMany / belongsTo |
| Tipado | Mixto (array, sin cast) | Strict types + casts (integer, decimal, date, boolean) |
| Transacciones | Ninguna | DB::transaction en Pago y BookingService |
| Migraciones | SQL dumps sin versionar | Migraciones Laravel versionadas |
| Seguridad SQL | Variables interpoladas en LIMIT | Parámetros con binding |
| Integridad financiera | Riesgo de incoherencia | Atómica (rollback automático) |
| Documentación | Comentarios dispersos | PHPDoc + bloques explicativos |
