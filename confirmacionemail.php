<?php

require 'conexion.php';



?>
<div class="confirmacion-content">

<h2 class="confirmacion-content_text">
<?php
// Código de confirmación de email para verificar la cuenta recibiendo el email por GET
if (isset($_GET['email'])) {
    $email = $_GET['email'];
    
    // Antes de hacer el Update se comprueba si el email ya está confirmado
    $stmt = $conn->prepare("SELECT validacion FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    
    if ($resultado && $resultado['validacion'] == 1) {
        echo "¡Hola! La cuenta ya había sido confirmada";
    } else {
        // Se prepara la consulta para actualizar el campo de confirmación de email
        $stmt = $conn->prepare("UPDATE usuarios SET validacion = 1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            echo "Operación exitosa: Se ha confirmado correctamente la cuenta de email. <br><strong>".$email."</strong>";
        } else {
            echo "Error: se ha producido un error en la confirmación de la cuenta.";
        }
    }
    
    $stmt->close();
} else {
    echo "Error: no se ha recibido ningún correo electrónico para confirmar.";
}

$conn->close();
?>
</h2>
<img class="confirmacion-content_img" src="/assets/images/confirm.svg" alt="">
<!-- Enlace a iniciar Sesión -->
<p>ya cuentas con una subscripcion?, si no es asi revisa nuestros planes  <a href="/AgendarCitasApp/planes.html">planes</a> </p> 
<br>
<p>si ya cuentas con alguna susbscripcion puede iniciar sesion</p>
<a class="confirmacion-content_cta" href="/AgendarCitasApp/iniciosesion.html">Iniciar Sesión</a>

</div>
<?php

?>
