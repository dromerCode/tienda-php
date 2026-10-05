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

// ---------- ACCIONES (añadir, cambiar cantidad, quitar) ----------
// Todas llegan por POST con un campo oculto "accion".
if (isset($_POST['accion'])) {
    if ($_POST['accion'] == 'anadir') {
        // El precio se saca de la BD, nunca del formulario: si no, cualquiera podría cambiarlo.
        $producto = $productoRepository->findById((int) $_POST['id_producto']);
        if ($producto) {
            $carritoRepository->addProducto($carrito->getId(), $producto->getId(), $producto->getPrecio());
        }
    } else {
        // Para cambiar o quitar, comprobamos que la línea es de este carrito.
        // Si no, alguien podría borrar líneas de otro cliente cambiando el id_linea.
        $idLinea = (int) $_POST['id_linea'];
        $esMia = false;
        foreach ($carritoRepository->findLineas($carrito->getId()) as $linea) {
            if ($linea->getIdLinea() == $idLinea) {
                $esMia = true;
            }
        }

        if ($esMia && $_POST['accion'] == 'cantidad') {
            $carritoRepository->cambiarCantidad($idLinea, (int) $_POST['cantidad']);
        } elseif ($esMia && $_POST['accion'] == 'quitar') {
            $carritoRepository->quitarLinea($idLinea);
        }
    }

    $carritoRepository->actualizarPrecioFinal($carrito->getId());
    // Redirigimos para que al recargar la página no se repita la acción.
    header('Location: index.php?page=carrito');
    exit();
}

$lineas = $carritoRepository->findLineas($carrito->getId());

// La línea solo guarda el id del producto: cargamos cada producto para mostrar su nombre.
// La clave es el id, así en la vista $productos[$linea->getIdProducto()] es su producto.
$productos = [];
foreach ($lineas as $linea) {
    $productos[$linea->getIdProducto()] = $productoRepository->findById($linea->getIdProducto());
}

require __DIR__ . '/../views/carritoView.phtml';
