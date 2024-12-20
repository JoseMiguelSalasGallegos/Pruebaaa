<?php
// conexión
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $alias = $_POST['alias'];
    $encargado = $_POST['encargado'];
    $id_medico = $_POST['medico'];
    $id_hospital = $_POST['hospital'];

    // Insertar datos
    $sql_insert = "INSERT INTO consultorios (direccion, telefono, email, alias, encargado, id_Medico, id_Hospital)
                   VALUES ('$direccion', '$telefono', '$email', '$alias', '$encargado', $id_medico, $id_hospital)";

    if ($conn->query($sql_insert) === TRUE) {
        echo "Consultorio registrado exitosamente.";
    } else {
        echo "Error al registrar consultorio: " . $conn->error;
    }
}

// Cerrar la conexión
$conn->close();
?>
