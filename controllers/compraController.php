<?php
require_once __DIR__ . '/../repositories/FacturaRepository.php';

if(!isset($_SESSION['id_cliente'])) {
    header("Location: index.php?page=login");
    exit;
}
$facturaRepo = new FacturaRepository($db);
$idCliente = (int)$_SESSION['id_cliente'];
$page = $_GET['page'] ?? 'pedido';
if ($page === 'finalizar_compra') {
        $idCarrito = $facturaRepo->findCarritoActivo($idCliente);

        if (!$idCarrito) {
            die("No se encontró ningún carrito activo para este cliente.");
        }

        $idFactura = $facturaRepo->finalizarCompra($idCliente, $idCarrito);

        if ($idFactura) {
            header("Location: index.php?page=pedido&id=" . $idFactura);
            exit;
        } else {
            die("El carrito está vacío o no se ha podido procesar.");
        }
    }
    if ($page === 'pedido') {
        $idFactura = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$idFactura) {
            header("Location: index.php?page=productos");
            exit;
        }

        $factura = $facturaRepo->findById($idFactura);


        if (!$factura || $factura->getIdCliente() !== $idCliente) {
            header("Location: index.php?page=productos");
            exit;
        }

        $lineas = $facturaRepo->findLineasByFactura($idFactura);

        require __DIR__ . '/../views/pedidoView.phtml';
    }

    if ($page === 'mis_pedidos') {
        $facturas = $facturaRepo->findByCliente($idCliente);
        require __DIR__ . '/../views/misPedidosView.phtml';
    }