<?php

$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "agendar-citas"; 


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';


$nombre = $_POST['nombre'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$direccion = $_POST['direccion'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$validacion = 0;  
$status = "activo";

        // Preparar la consulta para insertar en la base de datos
        $sentencia = $conn->prepare("INSERT INTO usuarios(Nombre,Email,Telefono,Direccion,password,Fecha_Nacimiento,Validacion,estado) VALUES (?,?,?,?,?,?,?,?);");
        $sentencia->bind_param($nombre, $email, $telefono, $direccion, $password, $fecha_nacimiento, $validacion, $status);
        /* $consulta->bindParam(1,$nombre);
        $consulta->bindParam(2,$email);
        $consulta->bindParam(3,$telefono);
        $consulta->bindParam(4,$direccion);
        $consulta->bindParam(5,$password);
        $consulta->bindParam(6,$fecha_nacimiento);
        $consulta->bindParam(7,$validacion);
        $consulta->bindParam(8,$status);
        */

        if ($sentencia->execute()) {
            echo "Operación exitosa: Se ha guardado correctamente";
        } else {
            echo "Error: se ha producido un error en el registro de la base de datos.";
        }
