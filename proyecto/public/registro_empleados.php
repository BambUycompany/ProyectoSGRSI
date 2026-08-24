<?php
require_once __DIR__ . "/../config/config.php";

session_start();


require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("administrador");

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AltaDatosUsuarios.php";
require_once RUTA_CONTROLADOR . "/prepararRegistroEmpleados.php";



require_once RUTA_VISTA . "/registro_empleados.php";
?>