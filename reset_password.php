<?php
header("Content-Type: application/json; charset=UTF-8");
require "conexion.php";

$correo = $_REQUEST["correo"] ?? null;
$pass   = $_REQUEST["pass"] ?? null;

if (!$correo || !$pass) {
    echo json_encode([
        "status" => "error",
        "message" => "Faltan parámetros",
        "codigo_debug" => "",
        "mail_error" => ""
    ]);
    exit;
}

try {
    $newPassHash = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "UPDATE login 
            SET passwordLogin = ?, 
                codigo_reset = NULL, 
                codigo_expira = NULL
            WHERE idusuario = (SELECT idusuario FROM usuario WHERE srtEmail = ?)";

    $stmt = $conn->prepare($sql);
    $success = $stmt->execute([$newPassHash, $correo]);

    if ($success && $stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "ok",
            "message" => "Contraseña actualizada",
            "codigo_debug" => "",
            "mail_error" => ""
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Correo no encontrado",
            "codigo_debug" => "",
            "mail_error" => ""
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error en la base de datos",
        "codigo_debug" => $e->getMessage(),
        "mail_error" => ""
    ]);
}
?>
