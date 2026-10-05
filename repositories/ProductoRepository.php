<?php
require_once __DIR__ . '/../models/Producto.php';

class ProductoRepository {
    private mysqli $db;
    public function __construct (mysqli $db){
        $this->db = $db;
    }
    public function findAll(): array {
        $sql = "SELECT id_producto, nombre, descripcion, precio FROM producto";
        $resultado =$this -> db->query($sql);
        $productos= [];

        if ($resultado){
            while ($row = $resultado->fetch_assoc()) {
                $productos[] = new Producto(
                    (int)$row['id_producto'],
                    $row['nombre'],
                    $row['descripcion'],
                    (float)$row['precio']
                
                );
            }
        }
        return $productos;
    }

    public function findById(int $id): ?Producto {
        $sql = "SELECT id_producto, nombre, descripcion, precio FROM producto WHERE id_producto = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($row = $resultado->fetch_assoc()){
                return new Producto(
                    (int)$row['id_producto'],
                    $row['nombre'],
                    $row['descripcion'],
                    (float)$row['precio']
                );
            }
            return null;
    }
}