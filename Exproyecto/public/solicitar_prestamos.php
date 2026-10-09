
<?php
require_once __DIR__ . "/../config/config.php";
session_start();

require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("solicitante");

require_once RUTA_CONTROLADOR . "/prepararSolicitudPrestamo.php";
require_once RUTA_VISTA . "/solicitar_prestamos.php";
?>