-- Esquema tienda online (MariaDB 10.2+)
-- Orden de creación respeta las claves foráneas.

CREATE DATABASE IF NOT EXISTS tienda
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tienda;

-- ---------------------------------------------------------
-- cliente
-- ---------------------------------------------------------
CREATE TABLE cliente (
  id_cliente  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(80)  NOT NULL,
  apellido    VARCHAR(120) NOT NULL,
  direccion   VARCHAR(255) NOT NULL,
  telefono    VARCHAR(20)  NOT NULL,
  email       VARCHAR(120) NOT NULL,
  -- Hash de password_hash(), nunca la contraseña en texto plano
  contrasena  VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_cliente),
  UNIQUE KEY uq_cliente_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- producto
-- ---------------------------------------------------------
CREATE TABLE producto (
  id_producto INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(120) NOT NULL,
  descripcion TEXT         NULL,
  precio      DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id_producto),
  CONSTRAINT chk_producto_precio CHECK (precio >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- carrito (cliente 1:N carrito)
-- ---------------------------------------------------------
CREATE TABLE carrito (
  id_carrito   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cliente   INT UNSIGNED NOT NULL,
  precio_final DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (id_carrito),
  CONSTRAINT fk_carrito_cliente
    FOREIGN KEY (id_cliente) REFERENCES cliente (id_cliente)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT chk_carrito_precio CHECK (precio_final >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- linea_producto (carrito 1:N linea, producto 1:N linea)
-- ---------------------------------------------------------
CREATE TABLE linea_producto (
  id_linea        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_carrito      INT UNSIGNED NOT NULL,
  id_producto     INT UNSIGNED NOT NULL,
  cantidad        INT UNSIGNED NOT NULL DEFAULT 1,
  -- Precio del producto copiado en el momento de la compra
  precio_unitario DECIMAL(10,2) NOT NULL,
  -- Subtotal calculado automáticamente
  subtotal        DECIMAL(10,2) AS (cantidad * precio_unitario) PERSISTENT,
  PRIMARY KEY (id_linea),
  -- Un mismo producto no se repite dentro del mismo carrito
  UNIQUE KEY uq_carrito_producto (id_carrito, id_producto),
  CONSTRAINT fk_linea_carrito
    FOREIGN KEY (id_carrito) REFERENCES carrito (id_carrito)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_linea_producto
    FOREIGN KEY (id_producto) REFERENCES producto (id_producto)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT chk_linea_cantidad CHECK (cantidad > 0),
  CONSTRAINT chk_linea_precio CHECK (precio_unitario >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- factura (carrito 1:1 factura, cliente 1:N factura)
-- ---------------------------------------------------------
CREATE TABLE factura (
  id_factura   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_carrito   INT UNSIGNED NOT NULL,
  id_cliente   INT UNSIGNED NOT NULL,
  precio_final DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id_factura),
  -- UNIQUE = una sola factura por carrito (relación 1:1)
  UNIQUE KEY uq_factura_carrito (id_carrito),
  CONSTRAINT fk_factura_carrito
    FOREIGN KEY (id_carrito) REFERENCES carrito (id_carrito)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_factura_cliente
    FOREIGN KEY (id_cliente) REFERENCES cliente (id_cliente)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT chk_factura_precio CHECK (precio_final >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Ejemplo: recalcular el precio_final de un carrito
-- ---------------------------------------------------------
-- UPDATE carrito c
-- SET c.precio_final = (
--   SELECT COALESCE(SUM(l.subtotal), 0)
--   FROM linea_producto l
--   WHERE l.id_carrito = c.id_carrito
-- )
-- WHERE c.id_carrito = 1;
