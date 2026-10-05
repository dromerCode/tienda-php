<?php
require_once __DIR__ . '/../repositories/ClienteRepository.php';
$clienteRepository = new ClienteRepository($db);
$mensaje = '';
$registrado = false;
$page = $_GET['page'] ?? 'login'; // Por defecto, mostramos la página de login
if ($page === 'registro' && isset($_POST['email'])) {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    // La contraseña sin trim: los espacios pueden formar parte de ella
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    if ($nombre == "" || $apellido == "" || $direccion == "" || $telefono == "" || $email == "" || $_POST['contrasena'] == "") {
        $mensaje = 'Rellena todos los campos';
    } elseif ($clienteRepository->findByEmail($email)) {
        $mensaje = 'Ese email ya está registrado';
    } else {
        $cliente = new Cliente(null, $nombre, $apellido, $direccion, $telefono, $email, $contrasena);
        $clienteRepository->save($cliente);
        $mensaje = 'Registro exitoso. Ahora puedes iniciar sesión.';
        $registrado = true;
    }
}
if ($page === 'registro') {
    require __DIR__ . '/../views/registerView.phtml';
} else {
    require __DIR__ . '/../views/loginView.phtml';
}