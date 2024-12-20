<?php
// Incluir la conexión a la base de datos
include 'conexion.php';  // Incluye el archivo de conexión

// Comprobar si el método es POST y si se ha enviado el valor 'seleccionar'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seleccionar'])) {
    $idUsuario = 1; // ID de usuario válido existente en la tabla Usuarios

    try {
        // Preparar la consulta para insertar en la tabla Suscripcion
        $sql = "INSERT INTO Suscripcion (id_Usuario, tipo) VALUES (:idUsuario, :tipo)";
        $stmt = $conn->prepare($sql);

        // Bindear los valores para la consulta
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', 'M', PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();
        echo "Se ha insertado 'M' correctamente en la tabla Suscripcion.";

        // Redireccionar después de la inserción
        header("Location: metodoPago.html");
        exit();
    } catch (PDOException $e) {
        // En caso de error en la consulta
        echo "Error al insertar en la base de datos: " . $e->getMessage();
    }
}
?>
