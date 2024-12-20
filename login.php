<?php
session_start(); // Iniciar sesión para gestionar las variables de sesión

require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar los campos obligatorios
    if (empty($_POST['email']) || empty($_POST['password'])) {
        die("Por favor, complete todos los campos.");
    }

    // Limpiar datos enviados
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Consulta para verificar el usuario en la base de datos
    $sql = "SELECT id_usuario, email, password_hash FROM usuarios WHERE email = '$email'";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        // Usuario encontrado, obtener datos
        $usuario = $resultado->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $usuario['password_hash'])) {
            // Guardar los datos relevantes del usuario en la sesión
            $_SESSION['user_id'] = $usuario['id_usuario']; // Corregido el índice
            $_SESSION['email'] = $usuario['email'];

            //prevenir el uso del botón atrás
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");

            // Redirigir al usuario a su página de inicio
            header("Location: inicioMedico.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
} else {
    echo "Método de solicitud no válido.";
}

$conn->close();
?>
