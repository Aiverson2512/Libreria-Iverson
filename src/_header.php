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
    :root {
      --ink: #172033;
      --ink-soft: #2b3854;
      --paper: #f7f4ee;
      --surface: #ffffff;
      --line: #e7e1d7;
      --accent: #e06c4f;
      --accent-dark: #bd4c35;
      --sage: #d9e5d6;
    }

    body {
      background: var(--paper);
      color: var(--ink);
      font-family: "Trebuchet MS", "Segoe UI", sans-serif;
      min-height: 100vh;
    }

    .navbar {
      background: rgba(23, 32, 51, .97) !important;
      border-bottom: 1px solid rgba(255, 255, 255, .1);
      box-shadow: 0 3px 18px rgba(23, 32, 51, .16);
    }

    .navbar-brand {
      font-size: 1.2rem;
      font-weight: 800;
      letter-spacing: .04em;
    }

    .navbar-brand i { color: #f5bf5a; }
    .navbar .nav-link { color: rgba(255, 255, 255, .76); padding: .7rem .8rem; }
    .navbar .nav-link:hover, .navbar .nav-link.active { color: #fff; }
    .navbar .nav-link.active { box-shadow: inset 0 -2px 0 var(--accent); }

    .hero-banner {
      position: relative;
      overflow: hidden;
      background: var(--ink);
      color: white;
      padding: 4.5rem 0 3.8rem;
      margin-bottom: 2.5rem;
    }

    .hero-banner::before {
      content: "";
      position: absolute;
      width: 22rem;
      height: 22rem;
      right: 6%;
      top: -13rem;
      border: 2px solid rgba(245, 191, 90, .45);
      border-radius: 50%;
    }

    .hero-banner::after {
      content: "";
      position: absolute;
      width: 9rem;
      height: 9rem;
      right: 18%;
      bottom: -5rem;
      background: var(--accent);
      border-radius: 50%;
      opacity: .88;
    }

    .hero-banner .container { position: relative; z-index: 1; }
    .hero-banner h1 { font-family: Georgia, "Times New Roman", serif; font-size: clamp(2rem, 4vw, 3rem); font-weight: 700; letter-spacing: -.035em; }
    .hero-banner p { color: #dce3ee; font-size: 1.06rem; max-width: 38rem; }

    .form-control, .form-select {
      border-color: var(--line);
      border-radius: .65rem;
      padding: .68rem .8rem;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 .22rem rgba(224, 108, 79, .16);
    }

    .btn { border-radius: .6rem; font-weight: 700; }
    .btn-primary { background: var(--accent); border-color: var(--accent); }
    .btn-primary:hover, .btn-primary:focus { background: var(--accent-dark); border-color: var(--accent-dark); }
    .btn-success { background: #2f7d68; border-color: #2f7d68; }
    .btn-warning { color: var(--ink); background: #f5bf5a; border-color: #f5bf5a; }

    .card {
      border: 1px solid var(--line);
      border-radius: 1rem;
      box-shadow: 0 10px 24px rgba(23, 32, 51, .055);
    }

    .table { --bs-table-bg: transparent; --bs-table-hover-bg: #f9ede8; }
    .table thead, .table-dark { background: var(--ink) !important; color: white; }
    .table thead th { font-size: .77rem; letter-spacing: .06em; text-transform: uppercase; }
    .table > :not(caption) > * > * { border-bottom-color: var(--line); padding: .9rem .75rem; }

    .badge-tipo {
      font-size: .72rem;
      padding: .38rem .65rem;
      border-radius: 999px;
      text-transform: capitalize;
    }

    .card-autor {
      transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
      background: var(--surface);
    }

    .card-autor:hover {
      transform: translateY(-5px);
      border-color: #d6a292;
      box-shadow: 0 16px 30px rgba(23, 32, 51, .12);
    }

    .avatar-circle {
      width: 56px;
      height: 56px;
      border-radius: 17px;
      background: linear-gradient(135deg, var(--accent), #f1a05e);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: white;
      font-weight: 800;
      box-shadow: 0 6px 14px rgba(224, 108, 79, .22);
    }

    .modal-content { border-radius: 1rem; }
    .modal-header { border-bottom-color: var(--line); }
    .modal-footer { border-top-color: var(--line); }
    .card-header.bg-primary { background: var(--ink) !important; }

    .page-contact .hero-banner { padding: 2.9rem 0 2.5rem; }
    .page-contact .form-control { padding: .52rem .75rem; }

    footer { background: var(--ink); color: #c8d1df; padding: 1.5rem 0; margin-top: 5rem; }
    footer a { color: #f5bf5a; text-decoration: none; }

    @media (max-width: 767.98px) {
      .hero-banner { padding: 3.25rem 0 3rem; margin-bottom: 1.75rem; }
      .hero-banner::before { right: -8rem; }
      .hero-banner::after { right: 8%; }
      .navbar .nav-link.active { box-shadow: inset 3px 0 0 var(--accent); }
    }
  </style>
</head>
<body class="page-<?= htmlspecialchars($paginaActiva ?? 'general', ENT_QUOTES, 'UTF-8') ?>">

<nav class="navbar navbar-expand-lg navbar-dark">
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
