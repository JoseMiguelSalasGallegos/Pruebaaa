<?php
//  conexión
include 'conexion.php';

// Obtener IDs de Médicos
$sql_medicos = "SELECT id_Medico FROM medicos";
$result_medicos = $conn->query($sql_medicos);

echo "IDs de Médicos:<br>";
if ($result_medicos->num_rows > 0) {
    while ($row = $result_medicos->fetch_assoc()) {
        echo $row['id_Medico'] . "<br>";
    }
} else {
    echo "No se encontraron médicos en la tabla.<br>";
}

// Insertar un consultorio
$id_medico = 1;   // Reemplazar con un ID real
$id_hospital = 1; // Reemplazar con un ID real
$sql_insert = "INSERT INTO consultorios (id_Medico, id_Hospital) VALUES ($id_medico, $id_hospital)";

if ($conn->query($sql_insert) === TRUE) {
    echo "Consultorio registrado exitosamente.<br>";
} else {
    echo "Error al registrar consultorio: " . $conn->error . "<br>";
}

// Cerrar la conexión
$conn->close();
?>
