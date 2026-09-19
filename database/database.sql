-- Base de datos de ITZA TATTOO
-- Esquema CANÓNICO del proyecto: coincide con el código (app/models/*).
-- Compatible con MySQL 8+ y MariaDB de XAMPP.

CREATE DATABASE IF NOT EXISTS itza_tattoo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE itza_tattoo;

SET NAMES utf8mb4;

-- =========================================================
-- TABLAS
-- =========================================================

-- Clientes, staff y administradores (login con roles).
CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  telefono VARCHAR(30) DEFAULT NULL,
  documento VARCHAR(50) DEFAULT NULL,
  fecha_nacimiento DATE DEFAULT NULL,
  rol ENUM('cliente', 'admin', 'tatuador') NOT NULL DEFAULT 'cliente',
  artista_id INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_usuarios_documento (documento),
  INDEX idx_usuarios_artist (artista_id)
) ENGINE=InnoDB;

-- Tatuadores que pueden atender citas.
CREATE TABLE IF NOT EXISTS artistas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED DEFAULT NULL,
  nombre VARCHAR(120) NOT NULL,
  bio TEXT DEFAULT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_artistas_user FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
  INDEX idx_artistas_user (usuario_id)
) ENGINE=InnoDB;

-- Servicios ofrecidos por el estudio.
CREATE TABLE IF NOT EXISTS servicios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  slug VARCHAR(100) NOT NULL UNIQUE,
  descripcion TEXT DEFAULT NULL,
  precio_desde DECIMAL(12,2) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Citas solicitadas por los clientes.
CREATE TABLE IF NOT EXISTS citas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  artista_id INT UNSIGNED NOT NULL,
  servicio_id INT UNSIGNED NOT NULL,
  fecha_cita DATE NOT NULL,
  hora_cita TIME NOT NULL,
  detalle_personalizado TEXT DEFAULT NULL,
  observaciones TEXT DEFAULT NULL,
  estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') NOT NULL DEFAULT 'pendiente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_citas_user FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_citas_artist FOREIGN KEY (artista_id) REFERENCES artistas(id),
  CONSTRAINT fk_citas_service FOREIGN KEY (servicio_id) REFERENCES servicios(id),
  INDEX idx_citas_date (fecha_cita, hora_cita),
  INDEX idx_citas_status (estado)
) ENGINE=InnoDB;

-- Abonos y pagos asociados a una cita.
CREATE TABLE IF NOT EXISTS abonos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cita_id INT UNSIGNED NOT NULL,
  monto DECIMAL(12,2) NOT NULL,
  metodo ENUM('nequi', 'transferencia', 'efectivo', 'tarjeta') NOT NULL,
  comprobante VARCHAR(255) DEFAULT NULL,
  estado ENUM('pendiente', 'verificado', 'rechazado', 'reembolsado') NOT NULL DEFAULT 'pendiente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_abonos_appointment FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE CASCADE,
  INDEX idx_abonos_status (estado)
) ENGINE=InnoDB;

-- Consentimiento informado del cliente.
CREATE TABLE IF NOT EXISTS consentimientos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cita_id INT UNSIGNED NOT NULL,
  nombre_cliente VARCHAR(120) NOT NULL,
  documento VARCHAR(50) NOT NULL,
  fecha_nacimiento DATE NOT NULL,
  procedimiento TEXT NOT NULL,
  acepta_riesgos TINYINT(1) NOT NULL DEFAULT 0,
  firma_cliente VARCHAR(120) NOT NULL,
  acudiente_nombre VARCHAR(120) DEFAULT NULL,
  acudiente_documento VARCHAR(50) DEFAULT NULL,
  parentesco VARCHAR(50) DEFAULT NULL,
  firma_acudiente VARCHAR(120) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_consentimientos_appointment FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE CASCADE,
  UNIQUE KEY uq_consentimientos_appointment (cita_id)
) ENGINE=InnoDB;

-- Mensajes enviados desde el formulario de contacto.
CREATE TABLE IF NOT EXISTS mensajes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  asunto VARCHAR(180) NOT NULL,
  mensaje TEXT NOT NULL,
  estado ENUM('nuevo', 'leido', 'respondido', 'archivado') NOT NULL DEFAULT 'nuevo',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_contact_status (estado),
  INDEX idx_contact_email (email)
) ENGINE=InnoDB;

-- Fotografías de trabajos del estudio.
CREATE TABLE IF NOT EXISTS galeria (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  artista_id INT UNSIGNED DEFAULT NULL,
  titulo VARCHAR(150) DEFAULT NULL,
  descripcion TEXT DEFAULT NULL,
  imagen VARCHAR(255) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_galeria_artist FOREIGN KEY (artista_id) REFERENCES artistas(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Horarios de disponibilidad por tatuador (gestionados individualmente, no afectan el sitio web global).
CREATE TABLE IF NOT EXISTS horarios_artistas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  artista_id INT UNSIGNED NOT NULL,
  dia_semana TINYINT(1) NOT NULL DEFAULT 0, -- 0=Dom, 1=Lun, ..., 6=Sáb
  hora_inicio TIME NOT NULL DEFAULT '09:00:00',
  hora_fin TIME NOT NULL DEFAULT '17:00:00',
  disponible TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_schedule_artist FOREIGN KEY (artista_id) REFERENCES artistas(id) ON DELETE CASCADE,
  UNIQUE KEY uq_artist_schedule (artista_id, dia_semana)
) ENGINE=InnoDB;

-- Promociones publicadas por el estudio.
CREATE TABLE IF NOT EXISTS promotions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  codigo VARCHAR(50) DEFAULT NULL UNIQUE,
  descuento DECIMAL(5,2) DEFAULT NULL,
  fecha_inicio DATE DEFAULT NULL,
  fecha_fin DATE DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_promotions_dates (fecha_inicio, fecha_fin)
) ENGINE=InnoDB;

-- Promociones usadas por los clientes.
CREATE TABLE IF NOT EXISTS canjes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  promocion_id INT UNSIGNED NOT NULL,
  usuario_id INT UNSIGNED NOT NULL,
  cita_id INT UNSIGNED DEFAULT NULL,
  used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_redemptions_promotion FOREIGN KEY (promocion_id) REFERENCES promotions(id) ON DELETE CASCADE,
  CONSTRAINT fk_redemptions_user FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_redemptions_appointment FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE SET NULL,
  UNIQUE KEY uq_redemption (promocion_id, usuario_id, cita_id)
) ENGINE=InnoDB;

-- =========================================================
-- DATOS INICIALES
-- =========================================================

-- Tatuadores
INSERT IGNORE INTO artistas (id, nombre, bio, foto)
VALUES
  (1, 'Itza — Blackwork & tribal', 'Tatuadora principal y fundadora de ITZA TATTOO. Especialista en blackwork y tribal.', 'img/itza tatto perfil.jpeg'),
  (2, 'Itza — Realismo', 'Retratos y escenas con sombreado fino y detalle fotográfico.', 'img/itza tatto perfil.jpeg'),
  (3, 'Itza — Fine line', 'Diseños minimalistas y delicados de línea fina.', 'img/itza tatto perfil.jpeg'),
  (4, 'Itza — Color & cover-up', 'Piezas a color y rediseño de tatuajes antiguos.', 'img/itza tatto perfil.jpeg');

-- Servicios (los "slug" coinciden con las opciones de los formularios).
INSERT IGNORE INTO servicios (nombre, slug, descripcion)
VALUES
  ('Blackwork', 'blackwork', 'Trazos sólidos, alto contraste, diseño geométrico y tribal.'),
  ('Realismo', 'realismo', 'Retratos y escenas con sombreado fino y detalle fotográfico.'),
  ('Fine Line', 'fine_line', 'Líneas delicadas para diseños minimalistas y delicados.'),
  ('Color', 'color', 'Piezas vibrantes con paletas personalizadas por artista.'),
  ('Cover-up', 'cover_up', 'Rediseño y cobertura de tatuajes antiguos.'),
  ('Piercing', 'piercing', 'Perforaciones con material estéril certificado.'),
  ('Diseño personalizado', 'personalizado', 'Diseños creados a partir de la idea de cada cliente.');

-- Promociones (los "codigo" son los que escribe el cliente).
INSERT IGNORE INTO promotions (nombre, descripcion, codigo, descuento, fecha_inicio, fecha_fin)
VALUES
  ('Bienvenida 20%', 'Válido para clientes nuevos en tatuajes desde $150.000', 'WELCOME20', 20.00, NULL, '2026-12-31'),
  ('Descuento ITZA 2026', 'Válido para tatuajes desde $100.000', 'ITZA2026', 15.00, NULL, '2026-12-31'),
  ('Regalo por referir', 'Por cada amigo que refiera y se tatúe', 'REFERIDOS', 10.00, NULL, '2026-12-31'),
  ('Cliente leal', 'Válido para clientes con más de 3 sesiones', 'LOYALTY', 25.00, NULL, '2026-12-31');

-- =========================================================
-- USUARIOS DE PRUEBA (Autenticación y roles)
-- Contraseña para TODOS: password   (cámbiala al publicar)
-- El hash abajo corresponde a "password" con password_hash() de PHP.
-- =========================================================
INSERT IGNORE INTO usuarios (nombre, email, password, telefono, documento, fecha_nacimiento, rol)
VALUES
  ('Administrador ITZA', 'admin@itza.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3001234567', '1000000001', '1990-01-01', 'admin'),
  ('Tatuador Itza',      'tatuador@itza.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3002345678', '1000000002', '1992-05-15', 'tatuador'),
  ('Cliente de Prueba',  'cliente@itza.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3003456789', '1000000003', '1995-08-20', 'cliente');

-- =========================================================
-- CONSULTAS Y REPORTES (apoyo para la rúbrica - APIs/Reportes)
-- =========================================================

DROP PROCEDURE IF EXISTS SP_REPORTE_CITAS;
DELIMITER $$
CREATE PROCEDURE SP_REPORTE_CITAS(IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT
        a.id                              AS id_cita,
        u.nombre                          AS cliente,
        u.email                           AS email_cliente,
        ar.nombre                         AS artista,
        s.nombre                          AS servicio,
        a.fecha_cita,
        a.hora_cita,
        a.estado,
        (SELECT IFNULL(SUM(p.monto), 0)
           FROM abonos p
          WHERE p.cita_id = a.id
            AND p.estado = 'verificado')  AS total_pagado
    FROM citas a
    JOIN usuarios    u  ON u.id  = a.usuario_id
    JOIN artistas  ar ON ar.id = a.artista_id
    JOIN servicios s  ON s.id  = a.servicio_id
    WHERE (p_desde IS NULL OR a.fecha_cita >= p_desde)
      AND (p_hasta IS NULL OR a.fecha_cita <= p_hasta)
    ORDER BY a.fecha_cita DESC, a.hora_cita DESC;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS SP_SALDO_CITA;
DELIMITER $$
CREATE PROCEDURE SP_SALDO_CITA(IN p_cita_id INT)
BEGIN
    SELECT
        a.id                              AS id_cita,
        a.estado,
        s.precio_desde                    AS precio_referencia,
        (SELECT IFNULL(SUM(p.monto), 0)
           FROM abonos p
          WHERE p.cita_id = a.id
            AND p.estado IN ('pendiente','verificado')) AS total_abonado
    FROM citas a
    JOIN servicios s ON s.id = a.servicio_id
    WHERE a.id = p_cita_id;
END $$
DELIMITER ;