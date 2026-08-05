<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

function generarIdAutor(PDO $pdo): string {
    for ($intento = 0; $intento < 10; $intento++) {
        $numero = str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        $id = substr($numero, 0, 3) . '-' . substr($numero, 3, 2) . '-' . substr($numero, 5);
        $stmt = $pdo->prepare('SELECT 1 FROM autores WHERE id_autor = ?');
        $stmt->execute([$id]);

        if (!$stmt->fetchColumn()) {
            return $id;
        }
    }

    throw new RuntimeException('No se pudo generar un código para el autor.');
}

$campos = [
    'nombre' => ['Nombre', 15],
    'apellido' => ['Apellido', 15],
    'telefono' => ['Teléfono', 12],
    'direccion' => ['Dirección', 20],
    'ciudad' => ['Ciudad', 15],
    'estado' => ['Estado', 2],
    'pais' => ['País', 3],
    'cod_postal' => ['Código postal', 5],
];
$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = trim($_POST['id_autor'] ?? '');

    try {
        if ($accion === 'crear' || $accion === 'editar') {
            $datos = [];
            foreach ($campos as $campo => [, $limite]) {
                $valor = trim($_POST[$campo] ?? '');
                if ($valor === '' || mb_strlen($valor) > $limite) {
                    throw new InvalidArgumentException("{$campos[$campo][0]} es obligatorio y no puede superar $limite caracteres.");
                }
                $datos[$campo] = $valor;
            }

            if (!ctype_digit($datos['cod_postal'])) {
                throw new InvalidArgumentException('El código postal solo puede contener números.');
            }

            if ($accion === 'crear') {
                $id = generarIdAutor($pdo);
                $stmt = $pdo->prepare('INSERT INTO autores (id_autor, apellido, nombre, telefono, direccion, ciudad, estado, pais, cod_postal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$id, $datos['apellido'], $datos['nombre'], $datos['telefono'], $datos['direccion'], $datos['ciudad'], strtoupper($datos['estado']), strtoupper($datos['pais']), $datos['cod_postal']]);
                $mensaje = "Autor creado con el código $id.";
            } else {
                if ($id === '') {
                    throw new InvalidArgumentException('Autor no válido.');
                }
                $stmt = $pdo->prepare('UPDATE autores SET apellido = ?, nombre = ?, telefono = ?, direccion = ?, ciudad = ?, estado = ?, pais = ?, cod_postal = ? WHERE id_autor = ?');
                $stmt->execute([$datos['apellido'], $datos['nombre'], $datos['telefono'], $datos['direccion'], $datos['ciudad'], strtoupper($datos['estado']), strtoupper($datos['pais']), $datos['cod_postal'], $id]);
                $mensaje = 'Autor actualizado correctamente.';
            }
        }

        if ($accion === 'eliminar') {
            if ($id === '') {
                throw new InvalidArgumentException('Autor no válido.');
            }
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM titulo_autor WHERE id_autor = ?');
            $stmt->execute([$id]);
            if ((int) $stmt->fetchColumn() > 0) {
                throw new RuntimeException('No se puede eliminar un autor que tiene libros asociados.');
            }
            $stmt = $pdo->prepare('DELETE FROM autores WHERE id_autor = ?');
            $stmt->execute([$id]);
            $mensaje = 'Autor eliminado correctamente.';
        }
    } catch (InvalidArgumentException | RuntimeException | PDOException $e) {
        $error = $e->getMessage();
    }
}

$autorEditando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM autores WHERE id_autor = ?');
    $stmt->execute([trim($_GET['editar'])]);
    $autorEditando = $stmt->fetch();
}

$autores = $pdo->query(
    'SELECT a.id_autor, a.nombre, a.apellido, a.telefono, a.ciudad, a.estado, COUNT(ta.id_titulo) AS total_libros
     FROM autores a
     LEFT JOIN titulo_autor ta ON ta.id_autor = a.id_autor
     GROUP BY a.id_autor, a.nombre, a.apellido, a.telefono, a.ciudad, a.estado
     ORDER BY a.apellido, a.nombre'
)->fetchAll();

$tituloPagina = 'Administrar Autores';
$paginaActiva = 'admin';
include '../_header.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people"></i> Autores</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver al panel</a>
    </div>
    <?php if ($mensaje): ?><div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4"><div class="card shadow-sm"><div class="card-body">
            <h5 class="card-title"><?= $autorEditando ? 'Editar autor' : 'Nuevo autor' ?></h5>
            <form method="post"><input type="hidden" name="accion" value="<?= $autorEditando ? 'editar' : 'crear' ?>">
                <?php if ($autorEditando): ?><input type="hidden" name="id_autor" value="<?= htmlspecialchars($autorEditando['id_autor']) ?>"><p class="small text-muted">Código: <code><?= htmlspecialchars($autorEditando['id_autor']) ?></code></p><?php endif; ?>
                <?php foreach ($campos as $campo => [$etiqueta, $limite]): ?>
                    <div class="mb-3"><label for="<?= $campo ?>" class="form-label"><?= $etiqueta ?> *</label><input type="<?= $campo === 'cod_postal' ? 'text' : 'text' ?>" class="form-control" id="<?= $campo ?>" name="<?= $campo ?>" maxlength="<?= $limite ?>" value="<?= htmlspecialchars($autorEditando[$campo] ?? '') ?>" required></div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary"><?= $autorEditando ? 'Guardar cambios' : 'Crear autor' ?></button>
                <?php if ($autorEditando): ?><a href="autores.php" class="btn btn-outline-secondary">Cancelar</a><?php endif; ?>
            </form>
        </div></div></div>
        <div class="col-lg-8"><div class="card shadow-sm"><div class="card-body"><div class="table-responsive">
            <table class="table table-hover align-middle mb-0"><thead class="table-dark"><tr><th>Autor</th><th>Teléfono</th><th>Ubicación</th><th>Libros</th><th>Acciones</th></tr></thead><tbody>
            <?php foreach ($autores as $autor): ?><tr>
                <td><?= htmlspecialchars(trim($autor['nombre']) . ' ' . trim($autor['apellido'])) ?></td><td><?= htmlspecialchars($autor['telefono']) ?></td><td><?= htmlspecialchars(trim($autor['ciudad']) . ', ' . $autor['estado']) ?></td><td><?= (int) $autor['total_libros'] ?></td><td>
                <a href="autores.php?editar=<?= urlencode($autor['id_autor']) ?>" class="btn btn-sm btn-warning">Editar</a><form method="post" class="d-inline"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id_autor" value="<?= htmlspecialchars($autor['id_autor']) ?>"><button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este autor?')">Eliminar</button></form>
                </td></tr><?php endforeach; ?>
            </tbody></table>
        </div></div></div></div>
    </div>
</div>
<?php include '../_footer.php'; ?>
