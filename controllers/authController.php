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

if ($page === 'login' && isset($_POST['email'])) {
    $cliente = $clienteRepository->findByEmail(trim($_POST['email']));

    // Mismo mensaje en los dos casos para no revelar qué emails están registrados
    if (!$cliente) {
        $mensaje = 'Email o contraseña incorrectos';
    } elseif (!password_verify($_POST['contrasena'], $cliente->getPassword())) {
        $mensaje = 'Email o contraseña incorrectos';
    } else {
        // El id hará falta en el carrito para saber de quién es
        $_SESSION['id_cliente'] = $cliente->getIdCliente();
        $_SESSION['nombre'] = $cliente->getNombre();
        header("Location: index.php");
        exit;
    }
}

if ($page === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

if ($page === 'registro') {
    require __DIR__ . '/../views/registerView.phtml';
} else {
    require __DIR__ . '/../views/loginView.phtml';
}