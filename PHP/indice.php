<?php

session_start();
require('conexion.php');


if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = []; 
}


$stmt = $pdo->query("SELECT * FROM productos ORDER BY creado_at DESC");
$productos = $stmt->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tienda - Productos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="#">iVlad Shop</a>
      <div>
        <a class="btn btn-outline-light" href="carrito.php">
          Carrito <span class="badge bg-danger"><?= array_sum($_SESSION['carrito']) ?: 0 ?></span>
        </a>
      </div>
    </div>
  </nav>

  <main class="container my-5">
    <h1 class="mb-4">Productos</h1>
    <div class="row g-4">
      <?php foreach ($productos as $p): ?>
        <div class="col-12 col-sm-6 col-md-4">
          <div class="card h-100 shadow-sm">
            <?php if ($p['imagen']): ?>
              <img src="../MEDIA/IMGS/<?= htmlspecialchars($p['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nombre']) ?>" style="height:220px;object-fit:cover;">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($p['nombre']) ?></h5>
              <p class="card-text text-muted small" style="flex:1"><?= nl2br(htmlspecialchars($p['descripcion'])) ?></p>
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <span class="fs-5 fw-bold">$<?= number_format($p['precio'],2) ?></span>
                </div>
                <form action="agregar_a_carrito.php" method="post" class="d-flex align-items-center">
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <label class="me-2 visually-hidden">Cantidad</label>
                  <input type="number" name="cantidad" value="1" min="1" max="<?= (int)$p['stock'] ?>" class="form-control form-control-sm me-2" style="width:70px;">
                  <button type="submit" class="btn btn-primary btn-sm" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
                    Añadir
                  </button>
                </form>
              </div>
              <?php if ($p['stock'] <= 0): ?>
                <div class="text-danger mt-2 small">Agotado</div>
              <?php else: ?>
                <div class="text-success mt-2 small">Stock: <?= (int)$p['stock'] ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <footer class="text-center py-4">
    <small class="text-muted">Ejemplo Tienda iVlad Shop BootStrap 5, PHP y PDO</small>
  </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>