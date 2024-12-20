<?php
header('Content-Type: application/json'); // Indica que la respuesta es JSON

//conexión
include 'conexion.php'; 

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Muestra los pacientes dependiendo de los términos en los que se busque 
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $sql = "SELECT id_Paciente, Nombre, Telefono, Email, Direccion, Edad FROM pacientes";
    if (!empty($search)) {
        $sql .= " WHERE Nombre LIKE ? OR Telefono LIKE ? OR Email LIKE ?";
    }

    $stmt = $conn->prepare($sql);

    if (!empty($search)) {
        $searchTerm = '%' . $search . '%';
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $pacientes = [];
    while ($row = $result->fetch_assoc()) {
        $pacientes[] = $row;
    }

    echo json_encode($pacientes);
}

if ($method === 'POST') {
    // Leer el cuerpo de la solicitud como JSON
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id_Paciente']) && !empty($data['id_Paciente'])) {
        $id_Paciente = $data['id_Paciente'];

        $sql = "SELECT * FROM pacientes WHERE id_Paciente = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_Paciente);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $paciente = $result->fetch_assoc();
            echo json_encode($paciente);
        } else {
            echo json_encode(["error" => "Paciente no encontrado"]);
        }
    } else {
        echo json_encode(["error" => "ID del paciente no proporcionado"]);
    }
}

$conn->close();
?>
