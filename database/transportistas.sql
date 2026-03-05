-- Tabla de Transportistas
-- Este script crea la tabla necesaria para el módulo de Transportistas

CREATE TABLE IF NOT EXISTS `transportistas` (
  `id_tta` int(11) NOT NULL AUTO_INCREMENT,
  `transportista` varchar(255) NOT NULL COMMENT 'Nombre del transportista (OBLIGATORIO)',
  `direccion` varchar(255) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `nacion` varchar(100) DEFAULT NULL,
  `mail_contacto` text DEFAULT NULL COMMENT 'Puede contener múltiples direcciones separadas por ";"',
  `telefono` varchar(50) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_tta`),
  KEY `idx_transportista` (`transportista`),
  KEY `idx_localidad` (`localidad`),
  KEY `idx_provincia` (`provincia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ejemplo de datos de prueba (opcional)
-- INSERT INTO `transportistas` (`transportista`, `direccion`, `localidad`, `codigo_postal`, `provincia`, `nacion`, `mail_contacto`, `telefono`, `comentarios`, `created_at`, `updated_at`) VALUES
-- ('7 DE AGOSTO', 'RUTA 22 Y ACCESO', 'BILE ALLEN', NULL, 'RIO NEGRO', 'ARGENTINA', NULL, '02941-450831', NULL, NOW(), NOW());

