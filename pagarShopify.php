<?php

require 'conexion.php';


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'pagar') {
        $codigoShopify = $_POST['Codigo_shopify'] ?? null;
        $fechaRegistro = $_POST['Fecha_registro'] ?? null;
        $idUsuario = 1; 

        if (!empty($codigoShopify) && !empty($fechaRegistro)) {
            // Verificar si el idUsuario existe en la tabla Usuarios
            $checkSql = "SELECT COUNT(*) FROM Usuarios WHERE id_Usuario = :idUsuario";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $checkStmt->execute();
            $exists = $checkStmt->fetchColumn();

            if ($exists) {
                // Insertar en la tabla Suscripcion
                $sql = "INSERT INTO Suscripcion (id_Usuario, codigo_shopify, fecha_registro, Tipo) 
                        VALUES (:idUsuario, :codigoShopify, :fechaRegistro, 'M')";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
                $stmt->bindValue(':codigoShopify', $codigoShopify, PDO::PARAM_STR);
                $stmt->bindValue(':fechaRegistro', $fechaRegistro, PDO::PARAM_STR);
                $stmt->execute();

                echo "Registro insertado correctamente en Suscripcion con Tipo 'M'.";
            } else {
                echo "Error: El id_Usuario especificado no existe en la tabla Usuarios.";
            }
        } else {
            echo "Error: Todos los campos son obligatorios.";
        }
    }
} catch (PDOException $e) {
    echo "Error al conectar o insertar en la base de datos: " . $e->getMessage();
}

?>