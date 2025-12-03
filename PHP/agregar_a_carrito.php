<?php

session_start();
require('conexion.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
if ($id === false || $id <= 0) {
    header('Location: indice.php');
    exit;
}
if ($cantidad === false || $cantidad <= 0) $cantidad = 1;


$stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
$stmt->execute([$id]);
$prod = $stmt->fetch();

if (!$prod) {
    header('Location: indice.php');
    exit;
}

$stock = (int)$prod['stock'];
if ($stock <= 0) {
    
    $_SESSION['mensaje'] = "Producto sin stock.";
    header('Location: indice.php');
    exit;
}


if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

$prev = isset($_SESSION['carrito'][$id]) ? (int)$_SESSION['carrito'][$id] : 0;
$nueva = $prev + $cantidad;


if ($nueva > $stock) $nueva = $stock;

$_SESSION['carrito'][$id] = $nueva;

header('Location: carrito.php');
exit;
?>