<?php
session_start();
session_destroy();
header("Location: inicioSesion.html");
exit();
?>
