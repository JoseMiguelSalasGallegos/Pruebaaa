<?php
session_start(); // Asegúrate de iniciar la sesión en cada archivo que necesite acceder a ella.

require 'conexion.php'; 

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario tiene una sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirigir al login si no hay sesión activa
    exit("No has iniciado sesión. Por favor, inicia sesión primero.");
}

$email = $_SESSION['email']; // Correo electrónico del usuario desde la sesión

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validar los datos del formulario
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['especialidad']) || empty($_POST['cedula'])) {
        die("Por favor, complete todos los campos.");
    }

    // Limpiar los datos enviados
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $especialidad = $conn->real_escape_string($_POST['especialidad']);
    $cedula = $conn->real_escape_string($_POST['cedula']);

    // Manejar la fotografía subida
    $ruta_destino = "uploads/";
    if (!is_dir($ruta_destino)) {
        mkdir($ruta_destino, 0755, true); // Crear la carpeta si no existe
    }

    $ruta_completa = null;
    if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = uniqid() . "_" . basename($_FILES['fotografia']['name']);
        $ruta_completa = $ruta_destino . $nombre_archivo;

        if (!move_uploaded_file($_FILES['fotografia']['tmp_name'], $ruta_completa)) {
            die("Error al subir la fotografía.");
        }
    }

    // Comprobar si ya existe un registro para el correo del usuario
    $sql_check = "SELECT id_medico FROM Medicos WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Si ya existe, actualizar los datos
        $sql_update = "UPDATE Medicos 
                       SET nombre = ?, telefono = ?, especialidad = ?, cedula = ?, fotografia = IFNULL(?, fotografia)
                       WHERE email = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssss", $nombre, $telefono, $especialidad, $cedula, $ruta_completa, $email);

        if ($stmt_update->execute()) {
            echo "Los datos del médico se han actualizado correctamente.";
        } else {
            echo "Error al actualizar los datos: " . $stmt_update->error;
        }
        $stmt_update->close();
    } else {
        // Si no existe, insertar un nuevo registro
        $sql_insert = "INSERT INTO Medicos (email, nombre, telefono, especialidad, cedula, fotografia)
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssss", $email, $nombre, $telefono, $especialidad, $cedula, $ruta_completa);

        if ($stmt_insert->execute()) {
            echo "Los datos del médico se han guardado correctamente.";
        } else {
            echo "Error al guardar los datos: " . $stmt_insert->error;
        }
        $stmt_insert->close();
    }

    $stmt_check->close();
} else {
    echo "Solicitud inválida.";
}

$conn->close();
?>
