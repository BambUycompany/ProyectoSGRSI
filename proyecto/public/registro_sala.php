
<?php
session_start();

require_once __DIR__ . "/../app/controlador/control_acceso.php";
requerirRol(["administrador", "soporte", "solicitante"]);

require_once __DIR__ . "/../app/vista/registro_sala.php";
?>