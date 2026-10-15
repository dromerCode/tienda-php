<?php
class Cliente{
    private $id_cliente;
    private string $nombre;
    private string $apellido;
    private string $direccion;
    private string $telefono;
    private string $email;
    private string $password;
    private string $rol;

    public function __construct($id, string $nombre, string $apellido, string $direccion, string $telefono, string $email, string $password, string $rol = 'cliente'){
        $this->id_cliente = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->password = $password;
        $this->rol = $rol;
    }

    public function getIdCliente(){
        return $this->id_cliente;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getApellido(){
        return $this->apellido;
    }
    public function getDireccion(){
        return $this->direccion;
    }
    public function getTelefono(){
        return $this->telefono;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getPassword(){
        return $this->password;
    }
    public function getRol(){
        return $this->rol;
    }


}
