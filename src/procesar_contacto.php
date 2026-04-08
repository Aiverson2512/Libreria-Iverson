<?php
require 'conexion.php';

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacto.php');
    exit;
}

// Leer y sanear datos del formulario 
$nombre     = trim($_POST['nombre']     ?? '');
$correo     = trim($_POST['correo']     ?? '');
$asunto     = trim($_POST['asunto']     ?? '');
$comentario = trim($_POST['comentario'] ?? '');

// Validaciones básicas del lado del servidor
if (empty($nombre) || empty($correo) || empty($asunto) || empty($comentario)) {
    header('Location: contacto.php?estado=error&mensaje=' . urlencode('Todos los campos son obligatorios.'));
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header('Location: contacto.php?estado=error&mensaje=' . urlencode('El correo electrónico no es válido.'));
    exit;
}

// Insertar en la tabla contacto usando PDO con sentencia preparada
try {
    $sql = "INSERT INTO contacto (fecha, correo, nombre, asunto, comentario)
            VALUES (NOW(), :correo, :nombre, :asunto, :comentario)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':correo',     $correo);
    $stmt->bindParam(':nombre',     $nombre);
    $stmt->bindParam(':asunto',     $asunto);
    $stmt->bindParam(':comentario', $comentario);
    $stmt->execute();

    header('Location: contacto.php?estado=ok');
    exit;

} catch (PDOException $e) {
    $errorMsg = 'No se pudo guardar el mensaje. Intenta más tarde.';
    header('Location: contacto.php?estado=error&mensaje=' . urlencode($errorMsg));
    exit;
}
?>
