<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';

// El esquema admite identificadores de hasta seis caracteres.
function generarIdUnico($pdo, $intentos = 0) {
    if ($intentos > 10) {
        throw new Exception('No se pudo generar un ID único después de 10 intentos.');
    }
    $id = 'LIB' . strtoupper(base_convert((string) random_int(0, 46655), 10, 36));
    $id = str_pad($id, 6, '0');
    $stmt = $pdo->prepare("SELECT id_titulo FROM titulos WHERE id_titulo = ?");
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        return generarIdUnico($pdo, $intentos + 1);
    }
    return $id;
}

$editoriales = $pdo->query('SELECT id_pub, nombre_pub FROM publicadores ORDER BY nombre_pub')->fetchAll();
$autores = $pdo->query('SELECT id_autor, nombre, apellido FROM autores ORDER BY apellido, nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $id_pub = trim($_POST['id_pub'] ?? '');
    $autoresSeleccionados = $_POST['autores'] ?? [];
    $precio = (float)($_POST['precio'] ?? 0);
    $avance = (float)($_POST['avance'] ?? 0);
    $total_ventas = (int)($_POST['total_ventas'] ?? 0);
    $notas = trim($_POST['notas'] ?? '');
    $fecha_pub = trim($_POST['fecha_pub'] ?? '');
    $contrato = isset($_POST['contrato']) ? 1 : 0;

    if (!is_array($autoresSeleccionados)) {
        $autoresSeleccionados = [];
    }

    $autoresSeleccionados = array_values(array_unique(array_filter($autoresSeleccionados, 'is_string')));

    if ($titulo && $tipo && $id_pub && $fecha_pub && $autoresSeleccionados) {
        if (count($autoresSeleccionados) > 9) {
            $error = 'Puedes asignar un máximo de 9 autores por libro.';
        } else {
        try {
            $id_titulo = generarIdUnico($pdo);
            $sql = "INSERT INTO titulos (id_titulo, titulo, tipo, id_pub, precio, avance, total_ventas, notas, fecha_pub, contrato)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id_titulo, $titulo, $tipo, $id_pub, $precio, $avance, $total_ventas, $notas, $fecha_pub, $contrato])) {
                $derechosBase = intdiv(100, count($autoresSeleccionados));
                $derechosRestantes = 100 % count($autoresSeleccionados);
                $stmtAutor = $pdo->prepare('INSERT INTO titulo_autor (id_autor, id_titulo, ord_au, derechos) VALUES (?, ?, ?, ?)');

                foreach ($autoresSeleccionados as $indice => $idAutor) {
                    $derechos = $derechosBase + ($indice < $derechosRestantes ? 1 : 0);
                    $stmtAutor->execute([$idAutor, $id_titulo, (string) ($indice + 1), $derechos]);
                }

                header('Location: index.php?mensaje=Libro+creado+exitosamente+con+ID+'.$id_titulo);
                exit;
            } else {
                $error = 'Error al crear el libro.';
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
        }
    } else {
        $error = 'El título, tipo, editorial, al menos un autor y la fecha de publicación son obligatorios.';
    }
}

$tituloPagina = 'Crear Libro';
$paginaActiva = 'admin';
include '../_header.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-plus-circle"></i> Crear Nuevo Libro</h2>
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
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tipo" class="form-label">Tipo (género) *</label>
                        <input type="text" class="form-control" id="tipo" name="tipo" placeholder="Ej: psychology, mod_cook" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="id_pub" class="form-label">Editorial *</label>
                        <select class="form-select" id="id_pub" name="id_pub" required>
                            <option value="">Selecciona una editorial</option>
                            <?php foreach ($editoriales as $editorial): ?>
                                <option value="<?= htmlspecialchars($editorial['id_pub']) ?>"><?= htmlspecialchars($editorial['nombre_pub']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="autores" class="form-label">Autor(es) *</label>
                        <select class="form-select" id="autores" name="autores[]" multiple size="6" required>
                            <?php foreach ($autores as $autor): ?>
                                <option value="<?= htmlspecialchars($autor['id_autor']) ?>">
                                    <?= htmlspecialchars(trim($autor['apellido']) . ', ' . trim($autor['nombre'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Mantén presionada Ctrl (o Cmd en Mac) para seleccionar varios autores.</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="avance" class="form-label">Avance</label>
                        <input type="number" step="0.01" class="form-control" id="avance" name="avance" value="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="total_ventas" class="form-label">Total Ventas</label>
                        <input type="number" class="form-control" id="total_ventas" name="total_ventas" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_pub" class="form-label">Fecha Publicación</label>
                        <input type="date" class="form-control" id="fecha_pub" name="fecha_pub" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="notas" class="form-label">Notas / Descripción</label>
                        <textarea class="form-control" id="notas" name="notas" rows="2"></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="contrato" name="contrato" value="1">
                            <label class="form-check-label" for="contrato">Bajo contrato</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar libro</button>
            </form>
        </div>
    </div>
</div>
<?php include '../_footer.php'; ?>
