<?php
session_start();
require_once 'conexion.php';

$error = '';

// Si ya está logueado, redirigir al panel admin
if (isset($_SESSION['usuario_id'])) {
    header('Location: admin/index.php');
    exit;
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario && $password) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = MD5(?)");
        $stmt->execute([$usuario, $password]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['usuario'];
            header('Location: admin/index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    } else {
        $error = 'Por favor, complete todos los campos.';
    }
}

// Incluir el header para usar el mismo diseño
$tituloPagina = 'Iniciar Sesión';
$paginaActiva = 'login';
include '_header.php';
?>

<div class="container" style="max-width: 500px; margin-top: 60px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
            <p class="mt-3 text-center"><a href="index.php">Volver al catálogo</a></p>
        </div>
    </div>
</div>

<?php include '_footer.php'; ?>