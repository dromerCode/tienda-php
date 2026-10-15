<?php
require_once __DIR__ . '/../models/Producto.php';

class ProductoRepository {
    private mysqli $db;
    public function __construct (mysqli $db){
        $this->db = $db;
    }
    public function findAll(): array {
        $sql = "SELECT id_producto, nombre, descripcion, precio, imagen FROM producto";
        $resultado =$this -> db->query($sql);
        $productos= [];

        if ($resultado){
            while ($row = $resultado->fetch_assoc()) {
                $productos[] = new Producto(
                    (int)$row['id_producto'],
                    $row['nombre'],
                    $row['descripcion'],
                    (float)$row['precio'],
                    $row['imagen']
                );
            }
        }
        return $productos;
    }

    public function findById(int $id): ?Producto {
        $sql = "SELECT id_producto, nombre, descripcion, precio, imagen FROM producto WHERE id_producto = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($row = $resultado->fetch_assoc()){
                return new Producto(
                    (int)$row['id_producto'],
                    $row['nombre'],
                    $row['descripcion'],
                    (float)$row['precio'],
                    $row['imagen']
                );
            }
            return null;
    }

    public function save(Producto $producto) {
        $stmt = $this->db->prepare("INSERT INTO producto (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
        $nombre = $producto->getNombre();
        $descripcion = $producto->getDescripcion();
        $precio = $producto->getPrecio();
        $imagen = $producto->getImagen();
        $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $imagen);
        $stmt->execute();
    }

    public function update(Producto $producto) {
        $stmt = $this->db->prepare("UPDATE producto SET nombre = ?, descripcion = ?, precio = ?, imagen = ? WHERE id_producto = ?");
        $id = $producto->getId();
        $nombre = $producto->getNombre();
        $descripcion = $producto->getDescripcion();
        $precio = $producto->getPrecio();
        $imagen = $producto->getImagen();
        $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $imagen, $id);
        $stmt->execute();
    }

    public function delete(int $id) {
        $stmt = $this->db->prepare("DELETE FROM producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    // Para saber si se puede borrar: si está en alguna línea de carrito, no
    public function estaEnAlgunCarrito(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM linea_producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        return $fila['total'] > 0;
    }
}