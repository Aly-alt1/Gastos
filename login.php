<?php
header('Content-Type: application/json');

// Configuración de base de datos
$servername = "localhost";
$username = "root";
$password = "5718912157189121";
$dbname = "bd_web_velvet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validar parámetros recibidos
    if (!isset($_REQUEST['correo']) || !isset($_REQUEST['pass'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Faltan parámetros"
        ]);
        exit;
    }

    $correo = $_REQUEST['correo'];
    $pass = $_REQUEST['pass'];

    // Preparar SP
    $stmt = $conn->prepare("CALL sp_validar_login(:correo, :pass)");

    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':pass', $pass);

    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Credenciales incorrectas"
        ]);
    } else {
        echo json_encode([
            "status" => "ok",
            "data" => $result
        ]);
    }

} catch(PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

$conn = null;
?>
