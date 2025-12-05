<?php
header("Content-Type: application/json; charset=UTF-8");
require "conexion.php";

$correo = $_REQUEST["correo"] ?? null;
$codigo = $_REQUEST["codigo"] ?? null;

if (!$correo || !$codigo) {
    echo json_encode([
        "status" => "error",
        "message" => "Faltan parámetros",
    ]);
    exit;
}

try {
    $sql = "SELECT idlogin FROM login
            WHERE codigo_reset = ?
            AND codigo_expira > NOW()
            AND idusuario = (SELECT idusuario FROM usuario WHERE srtEmail = ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$codigo, $correo]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "ok",
            "message" => "Código válido"
        ]);
    } else {
        echo json_encode([
            "status" => "invalid",
            "message" => "Código incorrecto o expirado"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error en la base de datos",
        "detail" => $e->getMessage()
    ]);
}
?>
