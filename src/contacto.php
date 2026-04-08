<?php
$tituloPagina = 'Librería Iverson – Contacto';
$paginaActiva = 'contacto';

// Leer mensaje de éxito/error enviado por procesar_contacto.php
$estado  = $_GET['estado']  ?? '';
$mensaje = $_GET['mensaje'] ?? '';

require '_header.php';
?>

<!-- Banner -->
<div class="hero-banner">
  <div class="container">
    <h1><i class="bi bi-envelope-open me-2"></i>Contáctanos</h1>
    <p class="mb-0">¿Tienes alguna pregunta? Escríbenos y te responderemos pronto.</p>
  </div>
</div>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-7">

      <!-- Alerta de estado -->
      <?php if ($estado === 'ok'): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <strong>¡Mensaje enviado!</strong> Gracias por contactarnos. Te responderemos pronto.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php elseif ($estado === 'error'): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Error:</strong> <?= htmlspecialchars($mensaje) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <!-- Formulario -->
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
          <h5 class="card-title mb-4">Formulario de contacto</h5>

          <form action="procesar_contacto.php" method="POST" id="formContacto" novalidate>

            <div class="mb-3">
              <label for="nombre" class="form-label fw-semibold">Nombre completo *</label>
              <input type="text" class="form-control" id="nombre" name="nombre"
                     placeholder="Ej: Juan Pérez" required maxlength="100">
              <div class="invalid-feedback">Por favor ingresa tu nombre.</div>
            </div>

            <div class="mb-3">
              <label for="correo" class="form-label fw-semibold">Correo electrónico *</label>
              <input type="email" class="form-control" id="correo" name="correo"
                     placeholder="ejemplo@correo.com" required maxlength="100">
              <div class="invalid-feedback">Por favor ingresa un correo válido.</div>
            </div>

            <div class="mb-3">
              <label for="asunto" class="form-label fw-semibold">Asunto *</label>
              <input type="text" class="form-control" id="asunto" name="asunto"
                     placeholder="Asunto de tu mensaje" required maxlength="150">
              <div class="invalid-feedback">Por favor ingresa el asunto.</div>
            </div>

            <div class="mb-3">
              <label for="comentario" class="form-label fw-semibold">Mensaje *</label>
              <textarea class="form-control" id="comentario" name="comentario"
                        rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
              <div class="invalid-feedback">Por favor escribe tu mensaje.</div>
              <div class="form-text text-end" id="charCount">0 / 500 caracteres</div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg" id="btnEnviar">
                <i class="bi bi-send me-2"></i>Enviar mensaje
              </button>
            </div>

          </form>
        </div>
      </div>

      <!-- Info de contacto -->
      <div class="row g-3 mt-2 mb-4">
        <div class="col-6">
          <div class="card border-0 bg-white shadow-sm text-center p-3">
            <i class="bi bi-clock fs-3 text-primary mb-2"></i>
            <small class="text-muted">Horario de atención</small>
            <p class="mb-0 fw-semibold">Lun–Vie 8am–6pm</p>
          </div>
        </div>
        <div class="col-6">
          <div class="card border-0 bg-white shadow-sm text-center p-3">
            <i class="bi bi-reply fs-3 text-success mb-2"></i>
            <small class="text-muted">Tiempo de respuesta</small>
            <p class="mb-0 fw-semibold">Menos de 24 horas</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
// Validación Bootstrap al enviar
const form = document.getElementById('formContacto');
form.addEventListener('submit', function (e) {
  if (!form.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
  }
  form.classList.add('was-validated');
});

// Contador de caracteres en el textarea
const textarea  = document.getElementById('comentario');
const charCount = document.getElementById('charCount');
textarea.addEventListener('input', function () {
  const len = this.value.length;
  charCount.textContent = `${len} / 500 caracteres`;
  charCount.style.color = len > 450 ? '#dc3545' : '#6c757d';
  this.maxLength = 500;
});
</script>

<?php require '_footer.php'; ?>
