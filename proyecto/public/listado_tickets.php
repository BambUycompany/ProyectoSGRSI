<?php
session_start();
require_once __DIR__ . "/../config/config.php";


require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("soporte");

require_once RUTA_VISTA . "/listado_tickets.php";
?>