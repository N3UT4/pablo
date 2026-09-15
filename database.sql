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
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  telefono VARCHAR(30) DEFAULT NULL,
  documento VARCHAR(50) DEFAULT NULL,
  fecha_nacimiento DATE DEFAULT NULL,
  rol ENUM('cliente', 'admin', 'tatuador') NOT NULL DEFAULT 'cliente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_users_documento (documento)
) ENGINE=InnoDB;

-- Tatuadores que pueden atender citas.
CREATE TABLE IF NOT EXISTS artists (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  bio TEXT DEFAULT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Servicios ofrecidos por el estudio.
CREATE TABLE IF NOT EXISTS services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  slug VARCHAR(100) NOT NULL UNIQUE,
  descripcion TEXT DEFAULT NULL,
  precio_desde DECIMAL(12,2) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Citas solicitadas por los clientes.
CREATE TABLE IF NOT EXISTS appointments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  artist_id INT UNSIGNED NOT NULL,
  service_id INT UNSIGNED NOT NULL,
  fecha_cita DATE NOT NULL,
  hora_cita TIME NOT NULL,
  detalle_personalizado TEXT DEFAULT NULL,
  observaciones TEXT DEFAULT NULL,
  estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') NOT NULL DEFAULT 'pendiente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_appointments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_appointments_artist FOREIGN KEY (artist_id) REFERENCES artists(id),
  CONSTRAINT fk_appointments_service FOREIGN KEY (service_id) REFERENCES services(id),
  INDEX idx_appointments_date (fecha_cita, hora_cita),
  INDEX idx_appointments_status (estado)
) ENGINE=InnoDB;

-- Abonos y pagos asociados a una cita.
CREATE TABLE IF NOT EXISTS payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT UNSIGNED NOT NULL,
  monto DECIMAL(12,2) NOT NULL,
  metodo ENUM('nequi', 'transferencia', 'efectivo', 'tarjeta') NOT NULL,
  comprobante VARCHAR(255) DEFAULT NULL,
  estado ENUM('pendiente', 'verificado', 'rechazado', 'reembolsado') NOT NULL DEFAULT 'pendiente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_payments_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
  INDEX idx_payments_status (estado)
) ENGINE=InnoDB;

-- Consentimiento informado del cliente.
CREATE TABLE IF NOT EXISTS consents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT UNSIGNED NOT NULL,
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
  CONSTRAINT fk_consents_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
  UNIQUE KEY uq_consents_appointment (appointment_id)
) ENGINE=InnoDB;

-- Mensajes enviados desde el formulario de contacto.
CREATE TABLE IF NOT EXISTS contact_messages (
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
CREATE TABLE IF NOT EXISTS gallery (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  artist_id INT UNSIGNED DEFAULT NULL,
  titulo VARCHAR(150) DEFAULT NULL,
  descripcion TEXT DEFAULT NULL,
  imagen VARCHAR(255) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_gallery_artist FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE SET NULL
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
CREATE TABLE IF NOT EXISTS promotion_redemptions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  promotion_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  appointment_id INT UNSIGNED DEFAULT NULL,
  used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_redemptions_promotion FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE CASCADE,
  CONSTRAINT fk_redemptions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_redemptions_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
  UNIQUE KEY uq_redemption (promotion_id, user_id, appointment_id)
) ENGINE=InnoDB;

-- =========================================================
-- DATOS INICIALES
-- =========================================================

-- Tatuadores
INSERT IGNORE INTO artists (id, nombre, bio, foto)
VALUES
  (1, 'Itza — Blackwork & tribal', 'Tatuadora principal y fundadora de ITZA TATTOO. Especialista en blackwork y tribal.', 'img/itza tatto perfil.jpeg'),
  (2, 'Itza — Realismo', 'Retratos y escenas con sombreado fino y detalle fotográfico.', 'img/itza tatto perfil.jpeg'),
  (3, 'Itza — Fine line', 'Diseños minimalistas y delicados de línea fina.', 'img/itza tatto perfil.jpeg'),
  (4, 'Itza — Color & cover-up', 'Piezas a color y rediseño de tatuajes antiguos.', 'img/itza tatto perfil.jpeg');

-- Servicios (los "slug" coinciden con las opciones de los formularios).
INSERT IGNORE INTO services (nombre, slug, descripcion)
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
INSERT IGNORE INTO users (nombre, email, password, telefono, documento, fecha_nacimiento, rol)
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
           FROM payments p
          WHERE p.appointment_id = a.id
            AND p.estado = 'verificado')  AS total_pagado
    FROM appointments a
    JOIN users    u  ON u.id  = a.user_id
    JOIN artists  ar ON ar.id = a.artist_id
    JOIN services s  ON s.id  = a.service_id
    WHERE (p_desde IS NULL OR a.fecha_cita >= p_desde)
      AND (p_hasta IS NULL OR a.fecha_cita <= p_hasta)
    ORDER BY a.fecha_cita DESC, a.hora_cita DESC;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS SP_SALDO_CITA;
DELIMITER $$
CREATE PROCEDURE SP_SALDO_CITA(IN p_appointment_id INT)
BEGIN
    SELECT
        a.id                              AS id_cita,
        a.estado,
        s.precio_desde                    AS precio_referencia,
        (SELECT IFNULL(SUM(p.monto), 0)
           FROM payments p
          WHERE p.appointment_id = a.id
            AND p.estado IN ('pendiente','verificado')) AS total_abonado
    FROM appointments a
    JOIN services s ON s.id = a.service_id
    WHERE a.id = p_appointment_id;
END $$
DELIMITER ;