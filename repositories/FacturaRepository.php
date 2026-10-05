<?php
    require_once __DIR__ . '/../models/Factura.php';

    class FacturaRepository {
        private mysqli $db;

        public function __construct(mysqli $db) {
            $this->db = $db;
        }

        public function save(Factura $factura): int {
            $stmt = $this->db->prepare("INSERT INTO factura (id_carrito, id_cliente, precio_final) VALUES (?, ?,
  ?)");
            $idCarrito = $factura->getIdCarrito();
            $idCliente = $factura->getIdCliente();
            $precioFinal = $factura->getPrecioFinal();

            $stmt->bind_param("iid", $idCarrito, $idCliente, $precioFinal);
            $stmt->execute();

            return (int)$this->db->insert_id;
        }

        public function findById(int $id) {
            $stmt = $this->db->prepare("SELECT id_factura, id_carrito, id_cliente, precio_final FROM factura WHERE
  id_factura = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $fila = $stmt->get_result()->fetch_assoc();

            if (!$fila) {
                return null;
            }

            return new Factura(
                (int)$fila['id_factura'],
                (int)$fila['id_carrito'],
                (int)$fila['id_cliente'],
                (float)$fila['precio_final']
            );
        }

        public function findByCliente(int $idCliente): array {
            $stmt = $this->db->prepare("SELECT id_factura, id_carrito, id_cliente, precio_final FROM factura WHERE
  id_cliente = ? ORDER BY id_factura DESC");
            $stmt->bind_param("i", $idCliente);
            $stmt->execute();

            $resultado = $stmt->get_result();

            $facturas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $facturas[] = new Factura(
                    (int)$fila['id_factura'],
                    (int)$fila['id_carrito'],
                    (int)$fila['id_cliente'],
                    (float)$fila['precio_final']
                );
            }
            return $facturas;
        }

        public function findLineasByFactura(int $idFactura): array {
            $sql = "SELECT lp.cantidad, lp.precio_unitario, lp.subtotal, p.nombre
                    FROM factura f
                    JOIN linea_producto lp ON f.id_carrito = lp.id_carrito
                    JOIN producto p ON lp.id_producto = p.id_producto
                    WHERE f.id_factura = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $idFactura);

            $stmt->execute();
            $resultado = $stmt->get_result();
            $lineas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $lineas[] = $fila;
            }
            return $lineas;
        }

        public function findCarritoActivo(int $idCliente) {
            $sql = "SELECT c.id_carrito
                    FROM carrito c
                    LEFT JOIN factura f ON c.id_carrito = f.id_carrito
                    WHERE c.id_cliente = ? AND f.id_factura IS NULL
                    ORDER BY c.id_carrito DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $idCliente);
            $stmt->execute();
            $fila = $stmt->get_result()->fetch_assoc();

            return $fila ? (int)$fila['id_carrito'] : null;
        }

        public function finalizarCompra(int $idCliente, int $idCarrito) {
            // 1. Calcular el total del carrito según sus líneas
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(subtotal), 0) AS total FROM linea_producto WHERE
  id_carrito = ?");
            $stmt->bind_param("i", $idCarrito);
            $stmt->execute();
            $total = (float)$stmt->get_result()->fetch_assoc()['total'];

            if ($total <= 0) {
                return null; 
            }
            $stmt = $this->db->prepare("UPDATE carrito SET precio_final = ? WHERE id_carrito = ?");
            $stmt->bind_param("di", $total, $idCarrito);
            $stmt->execute();

            $factura = new Factura(0, $idCarrito, $idCliente, $total);
            $idFactura = $this->save($factura);

            $stmt = $this->db->prepare("INSERT INTO carrito (id_cliente, precio_final) VALUES (?, 0.00)");
            $stmt->bind_param("i", $idCliente);
            $stmt->execute();

            return $idFactura;
        }
    }