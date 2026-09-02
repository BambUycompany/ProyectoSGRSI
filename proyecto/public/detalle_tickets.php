<?php
require_once __DIR__ . "/../config/config.php";

session_start();

require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("soporte");

require_once RUTA_CONTROLADOR . "/prepararDetalleTickets.php";
require_once RUTA_VISTA . "/detalle_tickets.php";
?>
