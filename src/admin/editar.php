<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$id = trim($_GET['id'] ?? '');
if ($id === '') {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM titulos WHERE id_titulo = ?");
$stmt->execute([$id]);
$libro = $stmt->fetch();
if (!$libro) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $avance = (float)($_POST['avance'] ?? 0);
    $total_ventas = (int)($_POST['total_ventas'] ?? 0);
    $notas = trim($_POST['notas'] ?? '');
    $fecha_pub = trim($_POST['fecha_pub'] ?? '');
    $contrato = isset($_POST['contrato']) ? 1 : 0;

    if ($titulo && $tipo && $fecha_pub) {
        $sql = "UPDATE titulos SET 
                    titulo = ?, tipo = ?, precio = ?, avance = ?, 
                    total_ventas = ?, notas = ?, fecha_pub = ?, contrato = ?
                WHERE id_titulo = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$titulo, $tipo, $precio, $avance, $total_ventas, $notas, $fecha_pub, $contrato, $id])) {
            header('Location: index.php?mensaje=Libro+actualizado+correctamente');
            exit;
        } else {
            $error = 'Error al actualizar el libro.';
        }
    } else {
        $error = 'El título, tipo y fecha de publicación son obligatorios.';
    }
}

$tituloPagina = 'Editar Libro';
$paginaActiva = 'admin';
include '../_header.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-pencil"></i> Editar Libro</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($libro['titulo']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tipo" class="form-label">Tipo (género) *</label>
                        <input type="text" class="form-control" id="tipo" name="tipo" value="<?= htmlspecialchars($libro['tipo']) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?= $libro['precio'] ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="avance" class="form-label">Avance</label>
                        <input type="number" step="0.01" class="form-control" id="avance" name="avance" value="<?= $libro['avance'] ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="total_ventas" class="form-label">Total Ventas</label>
                        <input type="number" class="form-control" id="total_ventas" name="total_ventas" value="<?= $libro['total_ventas'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_pub" class="form-label">Fecha Publicación</label>
                        <input type="date" class="form-control" id="fecha_pub" name="fecha_pub" value="<?= htmlspecialchars(substr($libro['fecha_pub'], 0, 10)) ?>" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="notas" class="form-label">Notas / Descripción</label>
                        <textarea class="form-control" id="notas" name="notas" rows="2"><?= htmlspecialchars($libro['notas']) ?></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="contrato" name="contrato" value="1" <?= $libro['contrato'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="contrato">Bajo contrato</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Actualizar libro</button>
            </form>
        </div>
    </div>
</div>
<?php include '../_footer.php'; ?>
