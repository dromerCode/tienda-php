-- Migración para BDs ya creadas (schema.sql ya incluye la columna en instalaciones nuevas):
--   docker exec -i tienda-db mariadb -u tienda -p tienda < db/imagenes.sql
USE tienda;

ALTER TABLE producto
  ADD COLUMN IF NOT EXISTS imagen VARCHAR(255) NULL;

-- Imágenes de los productos de ejemplo (están en img/productos)
UPDATE producto SET imagen = 'camiseta.jpg'   WHERE nombre = 'Camiseta básica';
UPDATE producto SET imagen = 'sudadera.jpg'   WHERE nombre = 'Sudadera con capucha';
UPDATE producto SET imagen = 'vaquero.jpg'    WHERE nombre = 'Pantalón vaquero';
UPDATE producto SET imagen = 'zapatillas.jpg' WHERE nombre = 'Zapatillas deportivas';
UPDATE producto SET imagen = 'gorra.jpg'      WHERE nombre = 'Gorra';
UPDATE producto SET imagen = 'mochila.jpg'    WHERE nombre = 'Mochila';
UPDATE producto SET imagen = 'calcetines.jpg' WHERE nombre = 'Calcetines (pack de 3)';
UPDATE producto SET imagen = 'botella.jpg'    WHERE nombre = 'Botella de agua';
