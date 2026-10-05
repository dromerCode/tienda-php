<?php
$mensaje = '';
$registrado = false;
$page = $_GET['page'] ?? 'login'; // Por defecto, mostramos la página de login
if ($page === 'registro') {
    require __DIR__ . '/../views/registerView.phtml';
} else {
    require __DIR__ . '/../views/loginView.phtml';
}