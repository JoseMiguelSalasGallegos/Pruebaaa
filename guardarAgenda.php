<?php
// Iniciar sesión
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['email'])) {
    die("Error: No tienes permiso para realizar esta acción. Por favor, inicia sesión.");
}

// conexion
include 'conexion.php';

// Obtener el email de la sesión
$email_sesion = $_SESSION['email'];

// Consultar el id_Medico basado en el email de la sesión
$sql_medico = "SELECT id_Medico FROM Medicos WHERE email = '$email_sesion'";
$result_medico = $conn->query($sql_medico);

if ($result_medico->num_rows > 0) {
    $row = $result_medico->fetch_assoc();
    $id_medico = $row['id_Medico'];
} else {
    die("Error: No se encontró un médico con el correo electrónico proporcionado.");
}

// Verificar si el formulario es para guardar o actualizar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["idConsultorio"])) {
        // Obtener datos de actualización
        $id_consultorio = intval($_POST["idConsultorio"]);
        $direccion = $conn->real_escape_string($_POST["direccion"]);
        $telefono = $conn->real_escape_string($_POST["telefono"]);
        $email = $conn->real_escape_string($_POST["email"]);
        $alias = $conn->real_escape_string($_POST["alias"]);
        $encargado = $conn->real_escape_string($_POST["encargado"]);

        // Validar email
        if ($email !== $email_sesion) {
            die("Error: El correo electrónico no coincide con el de la sesión.");
        }

        // Actualizar consultorio
        $sql_update = "UPDATE Consultorios 
                       SET direccion = '$direccion', 
                           telefono = '$telefono', 
                           email = '$email', 
                           alias = '$alias', 
                           encargado = '$encargado' 
                       WHERE id_Consultorio = $id_consultorio AND id_Medico = $id_medico";

        if ($conn->query($sql_update) === TRUE) {
            echo "Consultorio actualizado exitosamente.";
        } else {
            echo "Error al actualizar el consultorio: " . $conn->error;
        }
    } else {
        // Obtener datos para insertar
        $direccion = $conn->real_escape_string($_POST['direccion']);
        $telefono = $conn->real_escape_string($_POST['telefono']);
        $email = $conn->real_escape_string($_POST['email']);
        $alias = $conn->real_escape_string($_POST['alias']);
        $encargado = $conn->real_escape_string($_POST['encargado']);

        // Validar email
        if ($email !== $email_sesion) {
            die("Error: El correo electrónico no coincide con el de la sesión.");
        }

        // Insertar consultorio
        $sql_insert = "INSERT INTO Consultorios (direccion, telefono, email, alias, encargado, id_Medico) 
                       VALUES ('$direccion', '$telefono', '$email', '$alias', '$encargado', '$id_medico')";

        if ($conn->query($sql_insert) === TRUE) {
            echo "Consultorio guardado exitosamente.";
        } else {
            echo "Error al guardar el consultorio: " . $conn->error;
        }
    }
}

// Cerrar la conexión
$conn->close();
?>
