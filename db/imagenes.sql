-- Migración para BDs ya creadas (schema.sql ya incluye la columna en instalaciones nuevas):
--   docker exec -i tienda-db mariadb -u tienda -p tienda < db/imagenes.sql
USE tienda;

ALTER TABLE producto
  ADD COLUMN IF NOT EXISTS imagen VARCHAR(255) NULL;
