<?php

session_start();
require('conexion.php');

$ids = $_POST['ids'] ?? [];
$qtys = $_POST['qtys'] ?? [];

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

if (!is_array($ids) || !is_array($qtys)) {
    header('Location: carrito.php');
    exit;
}


foreach ($ids as $i => $idVal) {
    $id = (int)$idVal;
    $qty = isset($qtys[$i]) ? (int)$qtys[$i] : 1;
    if ($id <= 0) continue;
    if ($qty <= 0) {
        unset($_SESSION['carrito'][$id]);
        continue;
    }
    
    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    $stock = $prod ? (int)$prod['stock'] : 0;
    if ($stock <= 0) {
        unset($_SESSION['carrito'][$id]);
        continue;
    }
    if ($qty > $stock) $qty = $stock;
    $_SESSION['carrito'][$id] = $qty;
}

header('Location: carrito.php');
exit;
?>