<?php
    require_once __DIR__ . '/../db.php';   // deja lista $db con los datos del .env

    $page = $_GET['page'] ?? 'productos';

    if ($page == 'login' || $page == 'registro' || $page == 'logout') {
        require __DIR__ . '/authController.php';
    } elseif ($page == 'productos' || $page == 'producto') {
        require __DIR__ . '/productoController.php';
    } elseif ($page == 'finalizar_compra' || $page == 'pedido') {
        require __DIR__ . '/compraController.php';
    }