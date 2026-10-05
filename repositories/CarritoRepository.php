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
}

