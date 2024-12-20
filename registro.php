<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

//CONEXION BD 
require 'conexion.php';




// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefono = $_POST['telefono'];
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $validacion = 0; 
    $status = "activo";
    $fecha_nacimiento = $conn->real_escape_string($_POST['fecha_nacimiento']);
    
    
    // Verifica solo numeros 
    if (!preg_match("/^[0-9]+$/", $telefono)) {
        echo "Error: El número de teléfono solo debe contener dígitos.";
        exit(); 
    }
    $telefono = $conn->real_escape_string($telefono);

    // Validacion de email 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Error: El formato de correo electrónico no es válido.";
    } 
    
    elseif (!preg_match("/^[a-zA-Z0-9@._]*$/", $email)) {
        echo "Error: El correo electrónico contiene caracteres no permitidos.";
    } else {
        echo "Correo electrónico válido: " . $email;
    }

    // encriptacion de contraseña
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    } else {
        echo "Error: La contraseña es requerida.";
        exit();
    }
    try {
        // Configuración del correo
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth  = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host     = 'mail.jmcsoluciones.com';
        $mail->Port     = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Username  = 'residencias@jmcsoluciones.com';
        $mail->Password  = '1W4v4+Kl6lTu';
        $mail->setFrom('residencias@jmcsoluciones.com', 'jmcsoluciones');
        $mail->addAddress($email); // Dirección de correo del usuario
        $mail->Subject = 'jmcsoluciones, Confirmacion de cuenta';
        $mail->isHTML(true);
        $mail->Body = '<p>Gracias por registrarte, ' . $nombre . '! Confirma tu cuenta para completar el registro. </p> 
        <a href="http://localhost/AgendarCitasApp/confirmacionemail.php/user_confirmacionemail.php?email=' . $email . '">Confirmar Cuenta</a><p>';

       
    
        if ($mail->send()) {
            echo "Se ha enviado un correo de confirmacion a su direccion de correo electronico. Favor de revisar tu bandeja de entrada.";
        
    // Consulta para insertar datos 
    $sql = "INSERT INTO usuarios (nombre, email, telefono, direccion, password_hash, fecha_nacimiento, validacion, estado) 
            VALUES ('$nombre', '$email', '$telefono', '$direccion', '$password_hash', '$fecha_nacimiento', '$validacion','$status')";

    // Ejecutar la consulta e informar al usuario
    if ($conn->query($sql) === TRUE) {
        echo "Registro exitoso!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    
    $conn->close();
} else {
    echo "Error: No se pudo enviar el correo de confirmacion.";
}
} catch (Exception $e) {
echo "El mensaje no pudo ser enviado. Error de Mailer: {$mail->ErrorInfo}";
}
}

?>
