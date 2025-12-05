<?php
header("Content-Type: application/json; charset=UTF-8");
require "conexion.php"; // Crea $conn (PDO)

$correo = $_POST["correo"] ?? null;
$pass   = $_POST["pass"] ?? null;

if (!$correo || !$pass) {
    echo json_encode(["status" => "error", "message" => "Faltan parámetros"]);
    exit;
}

$newPassHash = password_hash($pass, PASSWORD_DEFAULT);

try {
    $sql = "UPDATE login 
            SET passwordLogin = ?
            WHERE idusuario = (SELECT idusuario FROM usuario WHERE srtEmail = ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$newPassHash, $correo]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "ok", "message" => "Contraseña actualizada"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Correo no encontrado"]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error en la base de datos",
        "detail" => $e->getMessage()
    ]);
}
?>
