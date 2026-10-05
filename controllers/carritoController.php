<?php
require_once __DIR__ . '/../repositories/CarritoRepository.php';
require_once __DIR__ . '/../repositories/ProductoRepository.php';

$carritoRepository = new CarritoRepository($db);
$productoRepository = new ProductoRepository($db);

if (!isset($_SESSION['id_cliente'])) {
    header('Location: index.php?page=login');
    exit();
}

$carrito = $carritoRepository->findAbierto($_SESSION['id_cliente']);

if (!$carrito) {
    $carritoRepository->crear($_SESSION['id_cliente']);
    $carrito = $carritoRepository->findAbierto($_SESSION['id_cliente']);
}

$lineas = $carritoRepository->findLineas($carrito->getId());

// La línea solo guarda el id del producto: cargamos cada producto para mostrar su nombre.
// La clave es el id, así en la vista $productos[$linea->getIdProducto()] es su producto.
$productos = [];
foreach ($lineas as $linea) {
    $productos[$linea->getIdProducto()] = $productoRepository->findById($linea->getIdProducto());
}

require __DIR__ . '/../views/carritoView.phtml';