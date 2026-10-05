<?php
    require_once __DIR__ . '/../repositories/ProductoRepository.php';
    require_once __DIR__ . '/../db.php';
    $repo = new ProductoRepository($db);

    // Si se recibe un 'id' por GET, mostramos el detalle; si no, el catálogo completo
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $producto = $repo->findById($id);

        if (!$producto) {
            header('Location: index.php');
            exit;
        }

        require_once __DIR__ . '/../views/productoView.phtml';
    } else {
        $productos = $repo->findAll();
        require_once __DIR__ . '/../views/productosView.phtml';
    }