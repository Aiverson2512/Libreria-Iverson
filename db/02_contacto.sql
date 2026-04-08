-- Tabla contacto para el formulario de contacto
CREATE TABLE IF NOT EXISTS `contacto` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `fecha`       DATETIME NOT NULL,
  `correo`      VARCHAR(100) NOT NULL,
  `nombre`      VARCHAR(100) NOT NULL,
  `asunto`      VARCHAR(150) NOT NULL,
  `comentario`  TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
