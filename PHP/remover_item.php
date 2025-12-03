<?php

session_start();

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

if (isset($_GET['vaciar'])) {
    unset($_SESSION['carrito']);
    header('Location: carrito.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id && isset($_SESSION['carrito'][$id])) {
    unset($_SESSION['carrito'][$id]);
}

header('Location: carrito.php');
exit;
?>