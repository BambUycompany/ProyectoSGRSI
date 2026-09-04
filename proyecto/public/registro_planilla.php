
<?php
require_once __DIR__ . "/../config/config.php";

session_start();

require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol(["administrador", "soporte", "solicitante"]);

require_once RUTA_CONTROLADOR . "/prepararRegistroPlanilla.php";
require_once RUTA_VISTA . "/registro_planilla.php";
?>