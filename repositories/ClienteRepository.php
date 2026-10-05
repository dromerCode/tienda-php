<?php

require_once __DIR__ . '/../models/Cliente.php';
class ClienteRepository{
    private mysqli $db;

    public function __construct(mysqli $db){
        $this->db = $db;
    }

    public function findByEmail(string $email): ?Cliente{
        $stmt = $this->db->prepare("SELECT id_cliente, nombre, apellido, direccion, telefono, email, contrasena FROM cliente WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();

        if (!$fila) {
            return null;
        }
        return new Cliente($fila['id_cliente'], $fila['nombre'], $fila['apellido'], $fila['direccion'], $fila['telefono'], $fila['email'], $fila['contrasena']);
    }

    public function save(Cliente $cliente){
        $stmt = $this->db->prepare("INSERT INTO cliente (nombre, apellido, direccion, telefono, email, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
        $nombre = $cliente->getNombre();
        $apellido = $cliente->getApellido();
        $direccion = $cliente->getDireccion();
        $telefono = $cliente->getTelefono();
        $email = $cliente->getEmail();
        $password = $cliente->getPassword();
        $stmt->bind_param("ssssss", $nombre, $apellido, $direccion, $telefono, $email, $password);
        $stmt->execute();
    }
}
