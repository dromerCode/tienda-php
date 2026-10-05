<?php

class Carrito {
    private $id;
    private int $id_cliente;
    private float $precio_final;

    public function __construct($id, int $id_cliente, float $precio_final) {
        $this->id = $id;
        $this->id_cliente = $id_cliente;
        $this->precio_final = $precio_final;
    }
    
    public function getId() {
        return $this->id;
    }
    public function getIdCliente() {
        return $this->id_cliente;
    }
    public function getPrecioFinal() {
        return $this->precio_final;
    }
}