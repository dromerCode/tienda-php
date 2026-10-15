<?php
class Producto {
    private ?int $id_producto;
    private string $nombre;
    private ?string $descripcion;
    private float $precio;
    private ?string $imagen;


public function __construct (?int $id_producto, string $nombre, ?string $descripcion, float $precio, ?string $imagen = null) {
    $this -> id_producto = $id_producto;
    $this->nombre = $nombre;
    $this -> descripcion = $descripcion;
    $this-> precio = $precio;
    $this->imagen = $imagen;

}
public function getId(): ?int { return $this-> id_producto;}
public function getNombre(): string {return $this->nombre;}
public function getDescripcion(): ?string {return $this->descripcion;}
public function getPrecio(): float { return $this->precio;}
public function getImagen(): ?string { return $this->imagen;}
}