<?php
require_once __DIR__ . '/../repositories/FacturaRepository.php';
require_once __DIR__ . '/../repositories/ClienteRepository.php';

// Todo el panel es solo para el admin
if (($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit;
}
$facturaRepo = new FacturaRepository($db);
$clienteRepo = new ClienteRepository($db);

// Si llega un id enseñamos ese pedido, si no la lista de pedidos y clientes
if (isset($_GET['id'])) {
    $factura = $facturaRepo->findById((int)$_GET['id']);

    if (!$factura) {
        header("Location: index.php?page=admin");
        exit;
    }

    $cliente = $clienteRepo->findById($factura->getIdCliente());
    $lineas = $facturaRepo->findLineasByFactura($factura->getId());
    require __DIR__ . '/../views/adminPedidoView.phtml';
} else {
    $pedidos = $facturaRepo->findAllConCliente();
    $clientes = $clienteRepo->findAll();
    require __DIR__ . '/../views/adminView.phtml';
}
