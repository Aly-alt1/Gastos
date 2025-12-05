<?php
header("Content-Type: application/json; charset=UTF-8");
require "conexion.php"; // crea $conn (PDO)

// -------------------------------
//  PHPMailer
// -------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

// Obtener el correo
$correo = $_REQUEST["correo"] ?? null;

if (!$correo) {
    echo json_encode(["status" => "error", "message" => "Falta correo"]);
    exit;
}

// Verificar si el correo existe
$sql = "SELECT idusuario FROM usuario WHERE srtEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$correo]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(["status" => "not_found"]);
    exit;
}

// Generar código y fecha
$codigo = rand(100000, 999999);
$expira = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Actualizar en login
$sql2 = "UPDATE login SET codigo_reset = ?, codigo_expira = ?
         WHERE idusuario = ?";
$stmt2 = $conn->prepare($sql2);
$stmt2->execute([$codigo, $expira, $user["idusuario"]]);

// -------------------------------
//  Enviar el correo con PHPMailer
// -------------------------------

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // 👉 PON TU CORREO Y TU CONTRASEÑA DE APLICACIÓN
    $mail->Username = 'yaralonso9121@gmail.com';
    $mail->Password = 'aasd rmuq rfvj armi'; // NO la de Gmail normal, solo la de aplicación

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('yaralonso9121@gmail.com', 'Recuperación de contraseña');
    $mail->addAddress($correo);

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Tu código de recuperación';
    $mail->Body = "<h2>Tu código es:</h2>
                   <h1>$codigo</h1>
                   <p>Este código expira en 10 minutos.</p>";

    $mail->send();

    echo json_encode([
        "status" => "ok",
        "message" => "Código enviado",
        "codigo_debug" => $codigo // quitar en producción
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "mail_error" => $mail->ErrorInfo
    ]);
}
?>
