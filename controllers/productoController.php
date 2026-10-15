<?php
    require_once __DIR__ . '/../repositories/ProductoRepository.php';
    require_once __DIR__ . '/../db.php';
    $repo = new ProductoRepository($db);
    $page = $_GET['page'] ?? 'productos';

    // Crear productos: solo el admin. A un cliente (o sin sesión) lo mandamos al catálogo
    if ($page === 'nuevo_producto') {
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $mensaje = '';
        if (isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);
            $precio = $_POST['precio'];

            if ($nombre == "" || $precio == "") {
                $mensaje = 'El nombre y el precio son obligatorios';
            } elseif ($precio < 0) {
                $mensaje = 'El precio no puede ser negativo';
            } else {
                $producto = new Producto(null, $nombre, $descripcion, (float)$precio);
                $repo->save($producto);
                header('Location: index.php?page=productos');
                exit;
            }
        }

        require_once __DIR__ . '/../views/nuevoProductoView.phtml';
    // Si se recibe un 'id' por GET, mostramos el detalle; si no, el catálogo completo
    } elseif (isset($_GET['id'])) {
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
