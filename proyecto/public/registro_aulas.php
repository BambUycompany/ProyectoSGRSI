<?php
require_once __DIR__ . "/../config/config.php";

session_start();

require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("administrador");

require_once RUTA_VISTA . "/registro_aulas.php";
?>