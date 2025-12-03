<?php

session_start();
require('conexion.php');

$carrito = $_SESSION['carrito'] ?? [];

$items = [];
$total = 0.00;

if (!empty($carrito)) {
    
    $ids = array_keys($carrito);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $byId = [];
    foreach ($productos as $p) $byId[$p['id']] = $p;

    foreach ($carrito as $id => $qty) {
        if (!isset($byId[$id])) continue;
        $p = $byId[$id];
        $subtotal = $p['precio'] * $qty;
        $total += $subtotal;
        $items[] = [
            'id' => $p['id'],
            'nombre' => $p['nombre'],
            'precio' => $p['precio'],
            'cantidad' => $qty,
            'stock' => $p['stock'],
            'imagen' => $p['imagen'],
            'subtotal' => $subtotal
        ];
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Carrito - iVlad Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="indice.php">iVlad Shop</a>
      <div>
        <a class="btn btn-outline-light" href="indice.php">Seguir comprando</a>
      </div>
    </div>
  </nav>

  <main class="container my-5">
    <h1 class="mb-4">Tu carrito</h1>

    <?php if (empty($items)): ?>
      <div class="alert alert-info">Tu carrito está vacío. <a href="indice.php" class="alert-link">Ver productos</a></div>
    <?php else: ?>
      <div class="table-responsive">
        <form action="actualizar_carrito.php" method="post">
          <table class="table align-middle bg-white shadow-sm">
            <thead class="table-light">
              <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th style="width:140px">Cantidad</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <?php if ($it['imagen']): ?>
                        <img src="../MEDIA/IMGS/<?= htmlspecialchars($it['imagen']) ?>" alt="" style="width:70px;height:70px;object-fit:cover" class="me-3 rounded">
                      <?php endif; ?>
                      <div>
                        <div class="fw-bold"><?= htmlspecialchars($it['nombre']) ?></div>
                        <div class="text-muted small">Stock: <?= (int)$it['stock'] ?></div>
                      </div>
                    </div>
                  </td>
                  <td>$<?= number_format($it['precio'],2) ?></td>
                  <td>
                    <input type="hidden" name="ids[]" value="<?= (int)$it['id'] ?>">
                    <div class="input-group input-group-sm">
                      <button class="btn btn-outline-secondary" type="button" onclick="changeQty(this,-1)">−</button>
                      <input type="number" name="qtys[]" class="form-control text-center" value="<?= (int)$it['cantidad'] ?>" min="1" max="<?= (int)$it['stock'] ?>">
                      <button class="btn btn-outline-secondary" type="button" onclick="changeQty(this,1)">+</button>
                    </div>
                  </td>
                  <td>$<?= number_format($it['subtotal'],2) ?></td>
                  <td>
                    <a href="remover_item.php?id=<?= (int)$it['id'] ?>" class="btn btn-sm btn-outline-danger">Eliminar</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr class="table-active">
                <td colspan="3" class="text-end fw-bold">Total:</td>
                <td colspan="2" class="fw-bold">$<?= number_format($total,2) ?></td>
              </tr>
            </tbody>
          </table>

          <div class="d-flex justify-content-between align-items-center">
            <div>
              <a href="indice.php" class="btn btn-secondary">Seguir comprando</a>
              <a href="pagar.php" class="btn btn-success ms-2">Ir a pagar</a>
            </div>

            <div>
              <button type="submit" class="btn btn-primary">Actualizar carrito</button>
              <a href="remover_item.php?vaciar=1" class="btn btn-outline-danger ms-2">Vaciar carrito</a>
            </div>
          </div>
        </form>
      </div>
    <?php endif; ?>

  </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function changeQty(btn, delta) {
  const input = btn.parentElement.querySelector('input[type="number"]');
  let val = parseInt(input.value) || 1;
  val += delta;
  if (val < parseInt(input.min)) val = parseInt(input.min);
  if (input.max && val > parseInt(input.max)) val = parseInt(input.max);
  input.value = val;
}
</script>
</body>
</html>