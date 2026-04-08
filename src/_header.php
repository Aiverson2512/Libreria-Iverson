<?php
// Recibe $paginaActiva = 'libros' | 'autores' | 'contacto'
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
    <a class="navbar-brand" href="index.php">
      <i class="bi bi-book-half me-2"></i>Librería Iverson
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= ($paginaActiva ?? '') === 'libros'   ? 'active fw-bold' : '' ?>" href="index.php">
            <i class="bi bi-journals me-1"></i>Libros
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($paginaActiva ?? '') === 'autores'  ? 'active fw-bold' : '' ?>" href="autores.php">
            <i class="bi bi-people me-1"></i>Autores
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($paginaActiva ?? '') === 'contacto' ? 'active fw-bold' : '' ?>" href="contacto.php">
            <i class="bi bi-envelope me-1"></i>Contacto
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
