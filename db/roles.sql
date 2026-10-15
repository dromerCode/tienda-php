-- Migración para BDs ya creadas (schema.sql ya incluye la columna en instalaciones nuevas):
--   docker exec -i tienda-db mariadb -u tienda -p tienda < db/roles.sql
USE tienda;

ALTER TABLE cliente
  ADD COLUMN IF NOT EXISTS rol ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente';

-- Para hacer admin a un usuario:
-- UPDATE cliente SET rol = 'admin' WHERE email = 'tu@email.com';
