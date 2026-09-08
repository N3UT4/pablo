-- Base de datos de ITZA TATTOO
-- Compatible con MySQL 8+ y MariaDB de XAMPP.

CREATE DATABASE IF NOT EXISTS itza_tattoo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE itza_tattoo;

-- Clientes y usuarios de acceso.
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

-- Datos iniciales del estudio.
INSERT IGNORE INTO artists (id, nombre, bio, foto)
VALUES (
  1,
  'Itza',
  'Tatuadora principal y fundadora de ITZA TATTOO.',
  'img/itza tatto perfil.jpeg'
);

INSERT IGNORE INTO services (nombre, slug, descripcion)
VALUES
  ('Puntillismo', 'puntillismo', 'Diseños construidos con puntos y degradados detallados.'),
  ('Blackwork', 'blackwork', 'Trazos sólidos, alto contraste y composiciones negras.'),
  ('Color', 'color', 'Piezas con color y paletas personalizadas.'),
  ('Geometría', 'geometria', 'Diseños geométricos, simétricos y de líneas precisas.'),
  ('Blackout', 'blackout', 'Coberturas y piezas de alto contraste en tinta negra.'),
  ('Diseños personalizados', 'personalizado', 'Diseños creados a partir de la idea de cada cliente.');
