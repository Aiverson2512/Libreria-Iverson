<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Recibe $paginaActiva = 'libros' | 'autores' | 'contacto' | 'admin' | 'login'
$rutaRaiz = str_contains($_SERVER['SCRIPT_NAME'], '/admin/') ? '../' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $tituloPagina ?? 'Librería Iverson' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }

    .navbar-brand { font-size: 1.4rem; font-weight: 700; letter-spacing: 1px; }

    .hero-banner {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
      color: white;
      padding: 50px 0 40px;
      margin-bottom: 30px;
    }
    .hero-banner h1 { font-weight: 700; }
    .hero-banner p  { opacity: .8; }

    .table thead { background-color: #0f3460; color: white; }
    .table tbody tr:hover { background-color: #e8f0fe; }

    .badge-tipo {
      font-size: .75rem;
      padding: 4px 10px;
      border-radius: 20px;
      text-transform: capitalize;
    }

    .card-autor {
      transition: transform .2s, box-shadow .2s;
      border: none;
      box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }
    .card-autor:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 20px rgba(0,0,0,.15);
    }
    .avatar-circle {
      width: 56px; height: 56px;
      border-radius: 50%;
      background: linear-gradient(135deg,#0f3460,#533483);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem; color: white; font-weight: 700;
    }

    footer { background:#1a1a2e; color:#aaa; padding:20px 0; margin-top:60px; }
    footer a { color:#7faaff; text-decoration:none; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background:#0f3460;">
  <div class="container">
    <a class="navbar-brand" href="<?= $rutaRaiz ?>index.php">
      <i class="bi bi-book-half me-2"></i>Librería Iverson
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <!-- LIBROS -->
        <li class="nav-item">
          <a class="nav-link <?= ($paginaActiva ?? '') === 'libros' ? 'active fw-bold' : '' ?>" href="<?= $rutaRaiz ?>index.php">
            <i class="bi bi-journals me-1"></i>Libros
          </a>
        </li>

        <!-- ADMIN / LOGIN / LOGOUT (condicional) -->
        <?php if (isset($_SESSION['usuario_id'])): ?>
          <li class="nav-item">
            <a class="nav-link <?= ($paginaActiva ?? '') === 'admin' ? 'active fw-bold' : '' ?>" href="<?= $rutaRaiz ?>admin/index.php">
              <i class="bi bi-shield-lock me-1"></i>Admin
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $rutaRaiz ?>logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link <?= ($paginaActiva ?? '') === 'login' ? 'active fw-bold' : '' ?>" href="<?= $rutaRaiz ?>login.php">
              <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
            </a>
          </li>
        <?php endif; ?>

        <!-- AUTORES -->
        <li class="nav-item">
          <a class="nav-link <?= ($paginaActiva ?? '') === 'autores' ? 'active fw-bold' : '' ?>" href="<?= $rutaRaiz ?>autores.php">
            <i class="bi bi-people me-1"></i>Autores
          </a>
        </li>

        <!-- CONTACTO -->
        <li class="nav-item">
          <a class="nav-link <?= ($paginaActiva ?? '') === 'contacto' ? 'active fw-bold' : '' ?>" href="<?= $rutaRaiz ?>contacto.php">
            <i class="bi bi-envelope me-1"></i>Contacto
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
