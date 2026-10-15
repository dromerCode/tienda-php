<?php
    require_once __DIR__ . '/../repositories/ProductoRepository.php';
    require_once __DIR__ . '/../db.php';
    $repo = new ProductoRepository($db);
    $page = $_GET['page'] ?? 'productos';

    // Gestionar productos es solo para el admin. A un cliente (o sin sesión) lo mandamos al catálogo
    $paginasAdmin = ['admin_productos', 'nuevo_producto', 'editar_producto', 'borrar_producto'];
    if (in_array($page, $paginasAdmin) && ($_SESSION['rol'] ?? '') !== 'admin') {
        header('Location: index.php');
        exit;
    }

    if ($page === 'admin_productos') {
        $mensaje = '';
        if (isset($_GET['error'])) {
            $mensaje = 'No se puede borrar ese producto porque está en algún carrito o pedido';
        }
        $productos = $repo->findAll();
        require_once __DIR__ . '/../views/adminProductosView.phtml';

    } elseif ($page === 'nuevo_producto') {
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
                header('Location: index.php?page=admin_productos');
                exit;
            }
        }

        require_once __DIR__ . '/../views/nuevoProductoView.phtml';

    } elseif ($page === 'editar_producto') {
        $producto = $repo->findById((int)($_GET['id'] ?? 0));
        if (!$producto) {
            header('Location: index.php?page=admin_productos');
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
                $producto = new Producto($producto->getId(), $nombre, $descripcion, (float)$precio);
                $repo->update($producto);
                header('Location: index.php?page=admin_productos');
                exit;
            }
        }

        require_once __DIR__ . '/../views/editarProductoView.phtml';

    } elseif ($page === 'borrar_producto') {
        // Se borra por POST (desde un formulario) para que no se pueda borrar solo con abrir un enlace
        if (isset($_POST['id_producto'])) {
            $id = (int)$_POST['id_producto'];

            // Si ya está en un carrito o en un pedido la BD no deja borrarlo (ON DELETE RESTRICT)
            if ($repo->estaEnAlgunCarrito($id)) {
                header('Location: index.php?page=admin_productos&error=1');
                exit;
            }
            $repo->delete($id);
        }
        header('Location: index.php?page=admin_productos');
        exit;

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
