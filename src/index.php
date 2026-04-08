<?php
require 'conexion.php';

$traduccionGeneros = [
    'business'     => 'Negocios',
    'mod_cook'     => 'Cocina Moderna',
    'popular_comp' => 'Informática Popular',
    'psychology'   => 'Psicología',
    'trad_cook'    => 'Cocina Tradicional',
    'UNDECIDED'    => 'Sin clasificar'
];

$tituloPagina = 'Librería Iverson – Libros';
$paginaActiva = 'libros';

$coloresTipo = [
    'business'     => 'primary',
    'mod_cook'     => 'success',
    'popular_comp' => 'info',
    'psychology'   => 'warning',
    'trad_cook'    => 'danger',
    'UNDECIDED'    => 'secondary',
];

// ← Se añadió avance y contrato para mostrarlos en el modal
$stmt = $pdo->query(
    "SELECT t.id_titulo, t.titulo, t.tipo, t.precio, t.total_ventas,
            t.fecha_pub, t.notas, t.avance, t.contrato,
            GROUP_CONCAT(CONCAT(a.nombre,' ',a.apellido) SEPARATOR ', ') AS autores_nombres
     FROM titulos t
     LEFT JOIN titulo_autor ta ON t.id_titulo = ta.id_titulo
     LEFT JOIN autores a       ON ta.id_autor  = a.id_autor
     GROUP BY t.id_titulo
     ORDER BY t.titulo ASC"
);
$libros = $stmt->fetchAll();
$totalLibros = count($libros);

require '_header.php';
?>

<!-- Banner -->
<div class="hero-banner">
  <div class="container">
    <h1><i class="bi bi-journals me-2"></i>Catálogo de Libros</h1>
    <p class="mb-0">Explora nuestra colección de <?= $totalLibros ?> títulos disponibles</p>
  </div>
</div>

<div class="container">

  <!-- Buscador JS -->
  <div class="row mb-3">
    <div class="col-md-5">
      <input type="text" id="buscador" class="form-control" placeholder="🔍 Buscar libro o autor...">
    </div>
    <div class="col-md-3 mt-2 mt-md-0">
      <select id="filtroTipo" class="form-select">
        <option value="">— Todos los géneros —</option>
        <?php
        $tipos = array_unique(array_column($libros, 'tipo'));
        foreach ($tipos as $t) {
            $nombreTraducido = $traduccionGeneros[$t] ?? $t;
            echo "<option value='$nombreTraducido'>$nombreTraducido</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-md-4 mt-2 mt-md-0 text-md-end">
      <span class="text-muted" id="contadorLibros">
        Mostrando <strong><?= $totalLibros ?></strong> libros
      </span>
    </div>
  </div>

  <p class="text-muted small mb-2">
    <i class="bi bi-hand-index me-1"></i>Haz clic en cualquier fila para ver los detalles del libro.
  </p>

  <!-- Tabla -->
  <div class="table-responsive shadow-sm rounded">
    <table class="table table-hover align-middle mb-0 bg-white" id="tablaLibros">
      <thead>
        <tr>
          <th>Título</th>
          <th>Género</th>
          <th>Precio</th>
          <th>Ventas totales</th>
          <th>Autor(es)</th>
          <th>Publicado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($libros as $libro): ?>
        <?php
          $tipo    = $libro['tipo'];
          $color   = $coloresTipo[$tipo] ?? 'secondary';
          $genero  = $traduccionGeneros[$tipo] ?? $tipo;
          $precio  = $libro['precio']       !== null ? '$' . number_format($libro['precio'], 2) : 'N/D';
          $ventas  = $libro['total_ventas'] !== null ? number_format($libro['total_ventas'])     : 'N/D';
          $avance  = $libro['avance']       !== null ? '$' . number_format($libro['avance'], 2)  : 'N/D';
          $fecha   = date('d/m/Y', strtotime($libro['fecha_pub']));
          $autores = htmlspecialchars($libro['autores_nombres'] ?? 'Sin autor asignado');
          $notas   = htmlspecialchars($libro['notas'] ?? '');
          $contrato = $libro['contrato'] === '1' ? 'Sí ✔' : 'No ✘';
        ?>
        <!-- data-* guarda los datos para el modal -->
        <tr class="fila-libro" style="cursor: pointer;"
            data-titulo="<?= htmlspecialchars($libro['titulo']) ?>"
            data-genero="<?= $genero ?>"
            data-color="<?= $color ?>"
            data-precio="<?= $precio ?>"
            data-ventas="<?= $ventas ?>"
            data-avance="<?= $avance ?>"
            data-autores="<?= $autores ?>"
            data-fecha="<?= $fecha ?>"
            data-contrato="<?= $contrato ?>"
            data-notas="<?= $notas ?>"
            data-id="<?= htmlspecialchars($libro['id_titulo']) ?>">
          <td>
            <strong><?= htmlspecialchars($libro['titulo']) ?></strong>
            <?php if ($libro['notas']): ?>
            <br><small class="text-muted"><?= htmlspecialchars(mb_substr($libro['notas'], 0, 80)) ?>…</small>
            <?php endif; ?>
          </td>
          <td><span class="badge bg-<?= $color ?> badge-tipo"><?= $genero ?></span></td>
          <td><?= $precio ?></td>
          <td><?= $ventas ?></td>
          <td><small><?= $autores ?></small></td>
          <td><small><?= $fecha ?></small></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <p class="text-muted mt-2 small">
    Total de registros en base de datos: <strong><?= $totalLibros ?></strong>
    &nbsp;|&nbsp; Función PHP: <code>count()</code>
  </p>
</div>

<!-- ===== MODAL DETALLE LIBRO ===== -->
<div class="modal fade" id="modalLibro" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <div class="modal-header text-white" id="modalHeader" style="background:#0f3460;">
        <h5 class="modal-title">
          <i class="bi bi-book me-2"></i><span id="mTitulo"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <p class="text-muted small mb-1">Género</p>
            <span id="mBadge" class="badge fs-6 mb-3"></span>

            <p class="text-muted small mb-1">Precio</p>
            <p class="fw-bold fs-5" id="mPrecio"></p>

            <p class="text-muted small mb-1">Avance del autor</p>
            <p class="fw-bold" id="mAvance"></p>

            <p class="text-muted small mb-1">Total de ventas</p>
            <p class="fw-bold" id="mVentas"></p>
          </div>
          <div class="col-md-6">
            <p class="text-muted small mb-1">Autor(es)</p>
            <p class="fw-bold" id="mAutores"></p>

            <p class="text-muted small mb-1">Fecha de publicación</p>
            <p class="fw-bold" id="mFecha"></p>

            <p class="text-muted small mb-1">Código</p>
            <p><code id="mId"></code></p>

            <p class="text-muted small mb-1">Bajo contrato</p>
            <p class="fw-bold" id="mContrato"></p>
          </div>
          <div class="col-12">
            <div class="p-3 rounded" style="background:#f1f5ff;">
              <p class="text-muted small mb-1">Descripción</p>
              <p class="mb-0" id="mNotas"></p>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cerrar
        </button>
      </div>

    </div>
  </div>
</div>

<script>
// ── Búsqueda y filtro (tu código original, sin cambios) ──
const buscador    = document.getElementById('buscador');
const filtroTipo  = document.getElementById('filtroTipo');
const filas       = document.querySelectorAll('#tablaLibros tbody tr');
const contador    = document.getElementById('contadorLibros');

function filtrar() {
  const texto = buscador.value.toLowerCase();
  const tipo  = filtroTipo.value.toLowerCase();
  let visibles = 0;

  filas.forEach(fila => {
    const contenido = fila.textContent.toLowerCase();
    const tipoFila  = fila.querySelector('td:nth-child(2)').textContent.toLowerCase().trim();
    const coincide  = contenido.includes(texto) && (tipo === '' || tipoFila === tipo);
    fila.style.display = coincide ? '' : 'none';
    if (coincide) visibles++;
  });

  contador.innerHTML = `Mostrando <strong>${visibles}</strong> libros`;
}

buscador.addEventListener('input', filtrar);
filtroTipo.addEventListener('change', filtrar);

// ── Modal: clic en cualquier fila ──
const coloresHex = {
  primary: '#0d6efd', success: '#198754', info: '#0dcaf0',
  warning: '#ffc107', danger:  '#dc3545', secondary: '#6c757d'
};

document.querySelectorAll('.fila-libro').forEach(fila => {
  fila.addEventListener('click', function () {
    const d = this.dataset;

    document.getElementById('mTitulo').textContent   = d.titulo;
    document.getElementById('mPrecio').textContent   = d.precio;
    document.getElementById('mVentas').textContent   = d.ventas;
    document.getElementById('mAvance').textContent   = d.avance;
    document.getElementById('mAutores').textContent  = d.autores;
    document.getElementById('mFecha').textContent    = d.fecha;
    document.getElementById('mId').textContent       = d.id;
    document.getElementById('mContrato').textContent = d.contrato;
    document.getElementById('mNotas').textContent    = d.notas || 'Sin descripción disponible.';

    const badge = document.getElementById('mBadge');
    badge.textContent = d.genero;
    badge.className   = `badge fs-6 bg-${d.color}`;

    document.getElementById('modalHeader').style.background = coloresHex[d.color] ?? '#0f3460';

    new bootstrap.Modal(document.getElementById('modalLibro')).show();
  });
});
</script>

<?php require '_footer.php'; ?>
