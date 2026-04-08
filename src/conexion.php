<?php
$host   = 'mysql';       // nombre del servicio en docker-compose
$dbname = 'dblibreria';
$user   = 'root';
$pass   = 'root123';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div style='font-family:sans-serif;color:red;padding:20px'>
         <h3>Error de conexión a la base de datos</h3>
         <p>" . $e->getMessage() . "</p>
         </div>");
}
?>
