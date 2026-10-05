<?php

class LineaProducto {
    private $id_linea;
    private int $id_carrito;
    private int $id_producto;
    private int $cantidad;
    private float $precio_unitario;
    private $subtotal;


    public function __construct($id_linea, int $id_carrito, int $id_producto, int $cantidad, float $precio_unitario, $subtotal = null) {
        $this->id_linea = $id_linea;
        $this->id_carrito = $id_carrito;
        $this->id_producto = $id_producto;
        $this->cantidad = $cantidad;
        $this->precio_unitario = $precio_unitario;
        $this->subtotal = $subtotal;
    }

    public function getIdLinea() {
        return $this->id_linea;
    }
    public function getIdCarrito() {
        return $this->id_carrito;
    }
    public function getIdProducto() {
        return $this->id_producto;
    }
    public function getCantidad() {
        return $this->cantidad;
    }
    public function getPrecioUnitario() {
        return $this->precio_unitario;
    }
    public function getSubtotal() {
        return $this->subtotal;
    }
}