<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

function generarIdEditorial(PDO $pdo): string {
    for ($intento = 0; $intento < 10; $intento++) {
        $id = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare('SELECT 1 FROM publicadores WHERE id_pub = ?');
        $stmt->execute([$id]);

        if (!$stmt->fetchColumn()) {
            return $id;
        }
    }

    throw new RuntimeException('No se pudo generar un código para la editorial.');
}

$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = trim($_POST['id_pub'] ?? '');

    try {
        if ($accion === 'crear' || $accion === 'editar') {
            $nombre = trim($_POST['nombre_pub'] ?? '');
            $ciudad = trim($_POST['ciudad'] ?? '');
            $estado = strtoupper(trim($_POST['estado'] ?? ''));

            if ($nombre === '' || $ciudad === '' || $estado === '') {
                throw new InvalidArgumentException('Nombre, ciudad y estado son obligatorios.');
            }

            if (mb_strlen($nombre) > 30 || mb_strlen($ciudad) > 15 || mb_strlen($estado) > 2) {
                throw new InvalidArgumentException('Uno o más campos exceden la longitud permitida.');
            }

            if ($accion === 'crear') {
                $id = generarIdEditorial($pdo);
                $stmt = $pdo->prepare('INSERT INTO publicadores (id_pub, nombre_pub, ciudad, estado) VALUES (?, ?, ?, ?)');
                $stmt->execute([$id, $nombre, $ciudad, $estado]);
                $mensaje = "Editorial creada con el código $id.";
            } else {
                if ($id === '') {
                    throw new InvalidArgumentException('Editorial no válida.');
                }

                $stmt = $pdo->prepare('UPDATE publicadores SET nombre_pub = ?, ciudad = ?, estado = ? WHERE id_pub = ?');
                $stmt->execute([$nombre, $ciudad, $estado, $id]);
                $mensaje = 'Editorial actualizada correctamente.';
            }
        }

        if ($accion === 'eliminar') {
            if ($id === '') {
                throw new InvalidArgumentException('Editorial no válida.');
            }

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM titulos WHERE id_pub = ?');
            $stmt->execute([$id]);

            if ((int) $stmt->fetchColumn() > 0) {
                throw new RuntimeException('No se puede eliminar una editorial que tiene libros asociados.');
            }

            $stmt = $pdo->prepare('DELETE FROM publicadores WHERE id_pub = ?');
            $stmt->execute([$id]);
            $mensaje = 'Editorial eliminada correctamente.';
        }
    } catch (InvalidArgumentException | RuntimeException | PDOException $e) {
        $error = $e->getMessage();
    }
}

$editorialEditando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT id_pub, nombre_pub, ciudad, estado FROM publicadores WHERE id_pub = ?');
    $stmt->execute([trim($_GET['editar'])]);
    $editorialEditando = $stmt->fetch();
}

$editoriales = $pdo->query(
    'SELECT p.id_pub, p.nombre_pub, p.ciudad, p.estado, COUNT(t.id_titulo) AS total_libros
     FROM publicadores p
     LEFT JOIN titulos t ON t.id_pub = p.id_pub
     GROUP BY p.id_pub, p.nombre_pub, p.ciudad, p.estado
     ORDER BY p.nombre_pub'
)->fetchAll();

$tituloPagina = 'Administrar Editoriales';
$paginaActiva = 'admin';
include '../_header.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-buildings"></i> Editoriales</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver al panel</a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= $editorialEditando ? 'Editar editorial' : 'Nueva editorial' ?></h5>
                    <form method="post">
                        <input type="hidden" name="accion" value="<?= $editorialEditando ? 'editar' : 'crear' ?>">
                        <?php if ($editorialEditando): ?>
                            <input type="hidden" name="id_pub" value="<?= htmlspecialchars($editorialEditando['id_pub']) ?>">
                            <p class="small text-muted">Código: <code><?= htmlspecialchars($editorialEditando['id_pub']) ?></code></p>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="nombre_pub" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre_pub" name="nombre_pub" maxlength="30" value="<?= htmlspecialchars($editorialEditando['nombre_pub'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ciudad" class="form-label">Ciudad *</label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad" maxlength="15" value="<?= htmlspecialchars($editorialEditando['ciudad'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <input type="text" class="form-control" id="estado" name="estado" maxlength="2" value="<?= htmlspecialchars($editorialEditando['estado'] ?? '') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= $editorialEditando ? 'Guardar cambios' : 'Crear editorial' ?></button>
                        <?php if ($editorialEditando): ?>
                            <a href="editoriales.php" class="btn btn-outline-secondary">Cancelar</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr><th>Código</th><th>Editorial</th><th>Ubicación</th><th>Libros</th><th>Acciones</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($editoriales as $editorial): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($editorial['id_pub']) ?></code></td>
                                        <td><?= htmlspecialchars($editorial['nombre_pub']) ?></td>
                                        <td><?= htmlspecialchars($editorial['ciudad']) ?>, <?= htmlspecialchars($editorial['estado']) ?></td>
                                        <td><?= (int) $editorial['total_libros'] ?></td>
                                        <td>
                                            <a href="editoriales.php?editar=<?= urlencode($editorial['id_pub']) ?>" class="btn btn-sm btn-warning">Editar</a>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id_pub" value="<?= htmlspecialchars($editorial['id_pub']) ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta editorial?')">Eliminar</button>
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
    </div>
</div>
<?php include '../_footer.php'; ?>
