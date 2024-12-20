<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "agendar-citas";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los consultorios
$sql = "SELECT direccion, telefono, email, alias, encargado FROM consultorios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="consultorio">';
        echo '<h3 class="consultorio-ubicacion">' . $row['direccion'] . '</h3>';
        echo '<p>Teléfono: ' . $row['telefono'] . '</p>';
        echo '<p>Email: ' . $row['email'] . '</p>';
        echo '<p>Alias: ' . $row['alias'] . '</p>';
        echo '<p>Encargado: ' . $row['encargado'] . '</p>';
        echo '</div>';
    }
} else {
    echo '<div class="mensaje-vacio">No hay consultorios registrados. ¡Comienza agregando uno!</div>';
}
?>
