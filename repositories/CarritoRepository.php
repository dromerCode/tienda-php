<?php

require_once __DIR__ . '/../models/Carrito.php';
require_once __DIR__ . '/../models/LineaProducto.php';

class CarritoRepository {
    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    public function findAbierto(int $idCliente): ?Carrito {
        $stmt = $this->db->prepare("SELECT c.id_carrito, c.id_cliente, c.precio_final FROM carrito c LEFT JOIN factura f ON f.id_carrito = c.id_carrito WHERE c.id_cliente = ? AND f.id_factura IS NULL");
        $stmt->bind_param("i", $idCliente);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();

        if (!$fila) {
            return null;
        }
        return new Carrito($fila['id_carrito'], $fila['id_cliente'], $fila['precio_final']);
    }

    public function crear(int $idCliente): int {
        $stmt = $this->db->prepare("INSERT INTO carrito (id_cliente, precio_final) VALUES (?, 0)");
        $stmt->bind_param("i", $idCliente);
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function findLineas(int $idCarrito): array {
        $stmt = $this->db->prepare("SELECT lp.id_linea, lp.id_carrito, lp.id_producto, lp.cantidad, lp.precio_unitario, lp.subtotal FROM linea_producto lp WHERE lp.id_carrito = ?");
        $stmt->bind_param("i", $idCarrito);
        $stmt->execute();
        $result = $stmt->get_result();

        $lineas = [];
        while ($fila = $result->fetch_assoc()) {
            $lineas[] = new LineaProducto($fila['id_linea'], $fila['id_carrito'], $fila['id_producto'], $fila['cantidad'], $fila['precio_unitario'], $fila['subtotal']);
        }
        return $lineas;
    }

    // Añade 1 unidad. Si el producto ya está en el carrito, choca con el UNIQUE
    // (id_carrito, id_producto) y en vez de dar error suma 1 a la cantidad.
    public function addProducto(int $idCarrito, int $idProducto, float $precioUnitario): void {
        $stmt = $this->db->prepare("INSERT INTO linea_producto (id_carrito, id_producto, cantidad, precio_unitario) VALUES (?, ?, 1, ?) ON DUPLICATE KEY UPDATE cantidad = cantidad + 1");
        $stmt->bind_param("iid", $idCarrito, $idProducto, $precioUnitario);
        $stmt->execute();
    }

    public function quitarLinea(int $idLinea): void {
        $stmt = $this->db->prepare("DELETE FROM linea_producto WHERE id_linea = ?");
        $stmt->bind_param("i", $idLinea);
        $stmt->execute();
    }

    public function cambiarCantidad(int $idLinea, int $nuevaCantidad): void {
        if ($nuevaCantidad <= 0) {
            $this->quitarLinea($idLinea);
        } else {
            $stmt = $this->db->prepare("UPDATE linea_producto SET cantidad = ? WHERE id_linea = ?");
            $stmt->bind_param("ii", $nuevaCantidad, $idLinea);
            $stmt->execute();
        }
    }

    public function actualizarPrecioFinal(int $idCarrito): void {
        $stmt = $this->db->prepare("UPDATE carrito c SET c.precio_final = (SELECT COALESCE(SUM(lp.subtotal), 0) FROM linea_producto lp WHERE lp.id_carrito = c.id_carrito) WHERE c.id_carrito = ?");
        $stmt->bind_param("i", $idCarrito);
        $stmt->execute();
    }
}

