<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$id = trim($_POST['id'] ?? '');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === '') {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM detalle_venta WHERE id_titulo = ?');
$stmt->execute([$id]);

if ((int) $stmt->fetchColumn() > 0) {
    header('Location: index.php?mensaje=No+se+puede+eliminar+un+libro+con+ventas+asociadas');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM titulo_autor WHERE id_titulo = ?');
$stmt->execute([$id]);

$stmt = $pdo->prepare('DELETE FROM titulos WHERE id_titulo = ?');
$stmt->execute([$id]);

header('Location: index.php?mensaje=Libro+eliminado+correctamente');
exit;
