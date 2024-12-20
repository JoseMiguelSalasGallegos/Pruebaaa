<?php
// Conexión a la base de datos
$host = "localhost";
$username = "root";
$password = "";
$database = "agendar-citas";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener IDs de médicos
$sql_medicos = "SELECT id_Medico, nombre FROM medicos";
$result_medicos = $conn->query($sql_medicos);

// Obtener IDs de hospitales
$sql_hospitales = "SELECT id_Hospital, nombre FROM hospitales";
$result_hospitales = $conn->query($sql_hospitales);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/styleDashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Mi Agenda Médica</title>
</head>
<body>
    <div class="container-agenda">
        <div class="header">
            <h1>Mi Agenda Médica</h1>
            <p>Organiza tus consultorios y horarios de manera sencilla</p>
        </div>

        <div id="formularioConsultorio" class="formulario">
            <h2>Datos del Nuevo Consultorio</h2>
            <form id="formNuevoConsultorio">
                <div class="campo">
                    <label for="ubicacion">Dirección:</label>
                    <input type="text" id="ubicacion" name="direccion" placeholder="Ejemplo: Av. Principal #123, Piso 2" required>
                </div>
                <div class="campo">
                    <label for="telefono">Número de Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="Ejemplo: 214-148-1254" maxlength="10" required>
                </div>
                <div class="campo">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" placeholder="Ejemplo: citasmedicas@software.com" required>
                </div>
                <div class="campo">
                    <label for="alias">Alias:</label>
                    <input type="text" id="alias" name="alias" placeholder="Ejemplo: Consultorio Principal" required>
                </div>
                <div class="campo">
                    <label for="encargado">Encargado:</label>
                    <input type="text" id="encargado" name="encargado" placeholder="Ejemplo: Dr. José Salas" required>
                </div>
                <div class="campo">
                    <label for="medico">Médico:</label>
                    <select id="medico" name="medico" required>
                        <option value="" disabled selected hidden>Selecciona tu Médico</option>
                        <?php while ($row = $result_medicos->fetch_assoc()): ?>
                            <option value="<?= $row['id_Medico'] ?>"><?= $row['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="campo">
                    <label for="hospital">Hospital:</label>
                    <select id="hospital" name="hospital" required>
                        <option value="" disabled selected hidden>Selecciona el Hospital</option>
                        <?php while ($row = $result_hospitales->fetch_assoc()): ?>
                            <option value="<?= $row['id_Hospital'] ?>"><?= $row['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="button" onclick="agregarConsultorio()" class="boton-grande">Guardar Consultorio</button>
            </form>
        </div>

        <div id="consultoriosContainer">
            <div class="mensaje-vacio" id="mensajeVacio">
                No hay consultorios registrados. ¡Comienza agregando uno!
            </div>
        </div>
    </div>

    <script src="consultorios.js"></script>
</body>
</html>
