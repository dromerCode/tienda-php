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
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = str_replace(',', '.', trim($_POST['precio'] ?? ''));

            if ($nombre == '' || $precio == '') {
                $mensaje = 'El nombre y el precio son obligatorios';
            } elseif (!is_numeric($precio) || $precio < 0) {
                $mensaje = 'El precio tiene que ser un número mayor o igual que 0';
            } else {
                $id = $repo->save($nombre, $descripcion == '' ? null : $descripcion, (float)$precio);
                header('Location: index.php?page=producto&id=' . $id);
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
