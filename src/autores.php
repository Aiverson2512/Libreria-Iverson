<?php
require 'conexion.php';

$tituloPagina = 'Librería Iverson – Autores';
$paginaActiva = 'autores';

// Consulta: autores con cantidad de libros y títulos de sus libros
$stmt = $pdo->query(
    "SELECT a.*,
            COUNT(ta.id_titulo) AS total_libros,
            GROUP_CONCAT(t.titulo SEPARATOR '|||') AS titulos_libros
     FROM autores a
     LEFT JOIN titulo_autor ta ON a.id_autor  = ta.id_autor
     LEFT JOIN titulos t       ON ta.id_titulo = t.id_titulo
     GROUP BY a.id_autor
     ORDER BY a.apellido ASC"
);
$autores = $stmt->fetchAll();
$totalAutores = count($autores);

require '_header.php';
?>

<!-- Banner -->
<div class="hero-banner">
  <div class="container">
    <h1><i class="bi bi-people me-2"></i>Nuestros Autores</h1>
    <p class="mb-0"><?= $totalAutores ?> autores registrados en la librería</p>
  </div>
</div>

<div class="container">

  <!-- Buscador -->
  <div class="row mb-4">
    <div class="col-md-5">
      <input type="text" id="buscadorAutor" class="form-control"
             placeholder="🔍 Buscar por nombre, apellido o ciudad...">
    </div>
    <div class="col-md-7 mt-2 mt-md-0 text-md-end align-self-center">
      <span class="text-muted" id="contadorAutores">
        Mostrando <strong><?= $totalAutores ?></strong> autores
      </span>
    </div>
  </div>

  <p class="text-muted small mb-3">
    <i class="bi bi-hand-index me-1"></i>Haz clic en una tarjeta para ver los detalles del autor.
  </p>

  <!-- Tarjetas de autores -->
  <div class="row g-3" id="gridAutores">
    <?php foreach ($autores as $autor): ?>
    <?php
      $iniciales   = strtoupper(
          mb_substr(trim($autor['nombre']), 0, 1) .
          mb_substr(trim($autor['apellido']), 0, 1)
      );
      $librosLabel = $autor['total_libros'] == 1 ? '1 libro' : $autor['total_libros'] . ' libros';
      $titulosRaw  = htmlspecialchars($autor['titulos_libros'] ?? '');
    ?>
    <div class="col-sm-6 col-lg-4 tarjeta-autor-col">
      <div class="card card-autor h-100 p-3" style="cursor:pointer;"
           data-nombre="<?= htmlspecialchars(trim($autor['nombre'])) ?>"
           data-apellido="<?= htmlspecialchars(trim($autor['apellido'])) ?>"
           data-iniciales="<?= $iniciales ?>"
           data-telefono="<?= htmlspecialchars($autor['telefono']) ?>"
           data-direccion="<?= htmlspecialchars($autor['direccion']) ?>"
           data-ciudad="<?= htmlspecialchars(trim($autor['ciudad'])) ?>"
           data-estado="<?= htmlspecialchars($autor['estado']) ?>"
           data-pais="<?= htmlspecialchars($autor['pais']) ?>"
           data-postal="<?= htmlspecialchars($autor['cod_postal']) ?>"
           data-libros="<?= $autor['total_libros'] ?>"
           data-titulos="<?= $titulosRaw ?>">

        <div class="d-flex align-items-center mb-3">
          <div class="avatar-circle me-3"><?= $iniciales ?></div>
          <div>
            <h6 class="mb-0 fw-bold">
              <?= htmlspecialchars(trim($autor['nombre'])) ?>
              <?= htmlspecialchars(trim($autor['apellido'])) ?>
            </h6>
            <small class="text-muted">
              <i class="bi bi-book me-1"></i><?= $librosLabel ?>
            </small>
          </div>
        </div>
        <ul class="list-unstyled small text-muted mb-0">
          <li><i class="bi bi-telephone me-2"></i><?= htmlspecialchars($autor['telefono']) ?></li>
          <li><i class="bi bi-geo-alt me-2"></i>
            <?= htmlspecialchars(trim($autor['ciudad'])) ?>,
            <?= htmlspecialchars($autor['estado']) ?>,
            <?= htmlspecialchars($autor['pais']) ?>
          </li>
          <li><i class="bi bi-mailbox me-2"></i><?= htmlspecialchars($autor['cod_postal']) ?></li>
        </ul>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <p class="text-muted mt-3 small">
    Total de autores: <strong><?= $totalAutores ?></strong>
    &nbsp;|&nbsp; Funciones PHP usadas: <code>count()</code>, <code>foreach</code>
  </p>
</div>

<!-- ===== MODAL DETALLE AUTOR ===== -->
<div class="modal fade" id="modalAutor" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <div class="modal-header text-white" style="background: linear-gradient(135deg,#0f3460,#533483);">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <div class="avatar-circle" id="mAvatar" style="width:40px;height:40px;font-size:1rem;"></div>
          <span id="mNombreCompleto"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-6">
            <p class="text-muted small mb-1">Teléfono</p>
            <p class="fw-bold" id="mTelefono"></p>
          </div>
          <div class="col-6">
            <p class="text-muted small mb-1">Libros publicados</p>
            <p class="fw-bold" id="mTotalLibros"></p>
          </div>
          <div class="col-12">
            <p class="text-muted small mb-1">Dirección</p>
            <p class="fw-bold" id="mDireccion"></p>
          </div>
          <div class="col-6">
            <p class="text-muted small mb-1">Ciudad / Estado</p>
            <p class="fw-bold" id="mCiudad"></p>
          </div>
          <div class="col-6">
            <p class="text-muted small mb-1">País / Cód. Postal</p>
            <p class="fw-bold" id="mPais"></p>
          </div>
          <!-- Lista de libros del autor -->
          <div class="col-12" id="seccionLibros">
            <div class="p-3 rounded" style="background:#f1f5ff;">
              <p class="text-muted small mb-2">Libros de este autor</p>
              <ul class="mb-0 ps-3" id="mListaLibros"></ul>
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
// ── Buscador (tu código original) ──
const buscadorAutor   = document.getElementById('buscadorAutor');
const colsAutores     = document.querySelectorAll('.tarjeta-autor-col');
const contadorAutores = document.getElementById('contadorAutores');

buscadorAutor.addEventListener('input', function () {
  const texto = this.value.toLowerCase();
  let visibles = 0;
  colsAutores.forEach(col => {
    const contenido = col.textContent.toLowerCase();
    const ok = contenido.includes(texto);
    col.style.display = ok ? '' : 'none';
    if (ok) visibles++;
  });
  contadorAutores.innerHTML = `Mostrando <strong>${visibles}</strong> autores`;
});

// ── Modal: clic en cualquier tarjeta ──
document.querySelectorAll('.card-autor').forEach(card => {
  card.addEventListener('click', function () {
    const d = this.dataset;

    document.getElementById('mAvatar').textContent      = d.iniciales;
    document.getElementById('mNombreCompleto').textContent = `${d.nombre} ${d.apellido}`;
    document.getElementById('mTelefono').textContent    = d.telefono;
    document.getElementById('mTotalLibros').textContent = d.libros + (d.libros == 1 ? ' libro' : ' libros');
    document.getElementById('mDireccion').textContent   = d.direccion;
    document.getElementById('mCiudad').textContent      = `${d.ciudad}, ${d.estado}`;
    document.getElementById('mPais').textContent        = `${d.pais} / ${d.postal}`;

    // Construir lista de títulos
    const lista = document.getElementById('mListaLibros');
    lista.innerHTML = '';
    const seccion = document.getElementById('seccionLibros');

    if (d.titulos && d.titulos.trim() !== '') {
      const titulos = d.titulos.split('|||');
      titulos.forEach(t => {
        const li = document.createElement('li');
        li.textContent = t.trim();
        lista.appendChild(li);
      });
      seccion.style.display = '';
    } else {
      seccion.style.display = 'none';
    }

    new bootstrap.Modal(document.getElementById('modalAutor')).show();
  });
});
</script>

<?php require '_footer.php'; ?>
