<?php
// conexion.php
// Configuración PDO (usa la misma configuración que tú compartiste)
$servername = "localhost";
$port = 3306;
$username = "root";
$password = "5718912157189121";
$dbname = "bd_web_velvet";

$dsn = "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // Respuesta JSON limpia si la conexión falla
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed", "detail" => $e->getMessage()]);
    exit;
}
?>
