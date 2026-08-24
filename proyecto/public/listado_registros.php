<?php
session_start();

require_once __DIR__ . "/../app/controlador/control_acceso.php";
requerirRol("soporte");

require_once __DIR__ . "/../app/controlador/prepararListadoRegistro.php";
require_once __DIR__ . "/../app/vista/listado_registros.php";
?>