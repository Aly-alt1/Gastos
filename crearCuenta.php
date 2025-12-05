<?php
$servername = "localhost:3306";
$username = "root";
$password = "5718912157189121";
$dbname = "bd_web_velvet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recibir datos de Android
    $nombreLogin = $_POST['usuario'];
    $email = $_POST['correo'];
    $passwordLogin = $_POST['pass'];

    // Datos faltantes para el SP
    $strNombre = $nombreLogin;
    $strApPaterno = "";
    $strApMaterno = "";
    $idrol = 1;

    // Preparar SP
    $stmt = $conn->prepare("CALL sp_Crear_Cuenta(
        :nombre,
        :apP,
        :apM,
        :email,
        :login,
        :pass,
        :rol
    )");

    // Bind variables
    $stmt->bindParam(':nombre', $strNombre);
    $stmt->bindParam(':apP', $strApPaterno);
    $stmt->bindParam(':apM', $strApMaterno);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':login', $nombreLogin);
    $stmt->bindParam(':pass', $passwordLogin);
    $stmt->bindParam(':rol', $idrol);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Cuenta creada correctamente"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No se pudo ejecutar el procedimiento"
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
