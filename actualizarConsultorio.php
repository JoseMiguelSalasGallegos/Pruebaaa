<?php
// Iniciar sesión para usar variables de sesión
session_start();

//conexión
include 'conexion.php';  

// Verificar si los datos fueron enviados por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar si el email está en la sesión
    if (!isset($_SESSION['email'])) {
        die("Error: No se ha definido el email en la sesión.");
    }

    // Obtener el email desde la sesión
    $email = $_SESSION['email'];

    // Recibir datos del formulario
    $direccion = $_POST['ubicacion'];
    $telefono = $_POST['telefono'];
    $alias = $_POST['alias'];
    $encargado = $_POST['encargado'];

    // Validación adicional
    if (empty($direccion) || empty($telefono) || empty($alias) || empty($encargado)) {
        die("Todos los campos son obligatorios.");
    }

    // Preparar la consulta para evitar inyección SQL
    $stmt = $conn->prepare("UPDATE consultorios SET direccion=?, telefono=?, alias=?, encargado=? WHERE email=?");
    $stmt->bind_param("sssss", $direccion, $telefono, $alias, $encargado, $email);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "Consultorio actualizado correctamente.";
    } else {
        echo "Error al actualizar el consultorio: " . $stmt->error;
    }

    // Cerrar la consulta
    $stmt->close();
    
} else {
    echo "Método de solicitud no válido.";
}

// Cerrar la conexión
$conn->close();
?>
