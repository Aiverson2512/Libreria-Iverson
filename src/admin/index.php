<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM titulos ORDER BY titulo ASC");
$libros = $stmt->fetchAll();

$tituloPagina = 'Administrar Libros';
$paginaActiva = 'admin';
include '../_header.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-journal-bookmark-fill"></i> Panel de Administración</h2>
        <div>
            <a href="../index.php" class="btn btn-outline-secondary"><i class="bi bi-house"></i> Ver sitio</a>
            <a href="autores.php" class="btn btn-outline-primary"><i class="bi bi-people"></i> Autores</a>
            <a href="editoriales.php" class="btn btn-outline-primary"><i class="bi bi-buildings"></i> Editoriales</a>
            <a href="crear.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Nuevo libro</a>
            <a href="../logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
        </div>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['mensaje']) ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Ventas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $libro): ?>
                        <tr>
                            <td><?= htmlspecialchars($libro['id_titulo']) ?></td>
                            <td><?= htmlspecialchars($libro['titulo']) ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($libro['tipo']) ?></span></td>
                            <td>$<?= number_format($libro['precio'] ?? 0, 2) ?></td>
                            <td><?= number_format($libro['total_ventas'] ?? 0) ?></td>
                            <td>
                                <a href="editar.php?id=<?= urlencode($libro['id_titulo']) ?>" class="btn btn-sm btn-warning">Editar</a>
                                <form method="post" action="eliminar.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id_titulo']) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este libro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../_footer.php'; ?>
