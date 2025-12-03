<?php

session_start();
require('conexion.php');

$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    header('Location: carrito.php');
    exit;
}


$ids = array_keys($carrito);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
$stmt->execute($ids);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$byId = [];
foreach ($productos as $p) $byId[$p['id']] = $p;

$total = 0;
foreach ($carrito as $id => $qty) {
    if (!isset($byId[$id])) continue;
    $total += $byId[$id]['precio'] * $qty;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    foreach ($carrito as $id => $qty) {
        
        $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ? FOR UPDATE");
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        if (!$p) continue;
        $nuevoStock = max(0, (int)$p['stock'] - (int)$qty);
        $stmt2 = $pdo->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt2->execute([$nuevoStock, $id]);
    }

    
    unset($_SESSION['carrito']);
    $_SESSION['mensaje_exito'] = "Pedido realizado correctamente. Gracias por su compra.";
    header('Location: indice.php');
    exit;
}

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Checkout - iVlad Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="indice.php">iVlad Shop</a>
    </div>
  </nav>

  <main class="container my-5">
    <h1>Checkout</h1>

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="card-title">Resumen de la compra</h5>
        <ul class="list-group list-group-flush mb-3">
          <?php foreach ($carrito as $id => $qty): ?>
            <?php if (!isset($byId[$id])) continue; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-bold"><?= htmlspecialchars($byId[$id]['nombre']) ?></div>
                <small class="text-muted">Cantidad: <?= (int)$qty ?></small>
              </div>
              <div>$<?= number_format($byId[$id]['precio'] * $qty,2) ?></div>
            </li>
          <?php endforeach; ?>
          <li class="list-group-item d-flex justify-content-between">
            <strong>Total</strong>
            <strong>$<?= number_format($total,2) ?></strong>
          </li>
        </ul>

        <form method="post">
         
          <button class="btn btn-success">Confirmar pedido</button>
          <a href="carrito.php" class="btn btn-link">Volver al carrito</a>
        </form>
      </div>
    </div>
  </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>