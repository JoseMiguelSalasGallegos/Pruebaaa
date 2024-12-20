<?php
session_start();


// Verificar si el usuario inició sesión
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); 
    exit();
}

// Evitar uso de caché para el botón de atras 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/assets/styleDashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Principal Médicos</title>
    <script src="/js/menuLateral.js"></script>
</head>
<body>
    <div class="container">
        <nav class="nav-sidebar">
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-links">
                <li><a href="inicioMedico.php"><i class="fas fa-home"></i><span>Inicio</span></a></li>
                <!-- <li><a href="configuracion.html"><i class="fas fa-cog"></i><span>Configuración</span></a></li> -->
                <li><a href="abrirAgenda.html"><i class="fas fa-cog"></i><span>Configuración</span></a></li>

                
                <li>
    <a href="logout.php" aria-label="Cerrar Sesión">
        <i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
    </a>
</li>

         
        </nav>
        <main class="content" id="content">
            <h1>Bienvenido</h1>
            <div class="iconos-inicio-dashboard">
                <!-- <a href="/html/medicosConf.html"><i class="fas fa-cog"></i></a> -->
                
                <img src="/assets/_img/icon Person.png">
                <li><a href="cuentaMedicoConf.html"><i class="fas fa-cog"></i><span>Configuración de Cuenta </span></a></li>
                <span>  <?php echo htmlspecialchars($_SESSION['email']); ?> </span>
               
                
            </div>
            <div class="dashboard">
                <div class="config-section">
                    <h2>Directorio de Pacientes</h2>
                    <div class="formMedicos">
                        <div class="parrafo1">
                            <p>En este módulo, el médico puede gestionar la información de sus pacientes de manera centralizada. Incluye funcionalidades para visualizar el historial clínico, añadir o actualizar datos médicos relevantes y realizar anotaciones de cada consulta. Este apartado permite agilizar el seguimiento de cada paciente, facilitando el acceso a sus antecedentes y optimizando el proceso de atención en cada visita. Además, el sistema asegura la privacidad de la información, cumpliendo con las normas de protección de datos en el sector salud.</p>
                        </div>
                        <img class="images-inicio-hospital" src="/assets/_img/directorioPacientes.png">
                        <button id="pacientesBtn" type="button" onclick="window.location.href='GestionPacientes.html';">
                            Acceder
                        </button>
                        
                    </div>
                </div>
                <div class="config-section hidden">
                    <h2>Control de Citas</h2>
                    <div class="formAgenda-Control">
                        <div class="parrafo1">
                            <p>Este módulo permite al médico gestionar todas las citas programadas de manera efectiva y organizada. El médico puede ver un calendario completo con las citas agendadas, programar nuevas citas, modificar o cancelar citas existentes, y recibir notificaciones sobre los cambios importantes. Además, el módulo facilita la coordinación de tiempos y espacios de consulta, permitiendo al médico optimizar su agenda y mejorar la experiencia de los pacientes con recordatorios automáticos y confirmaciones de asistencia.</p>
                            <button id="pacientesBtn" type="button" onclick="window.location.href='controlCitas.html';">
                            Acceder
                        </button>
                        </div>
                        <img class="images-inicio-medicos" src="/assets/_img/controlCitas.png">
                        <!-- <button id="pacientesBtn" type="button" onclick="window.location.href='GestionPacientes.html';">
                            Acceder
                        </button> -->
                        
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script defer src="/js/medicosConf.js"></script>
</body>
</html>