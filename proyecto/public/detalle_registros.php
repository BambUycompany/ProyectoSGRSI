<?php
session_start();

require_once __DIR__ . "/../app/controlador/control_acceso.php";
requerirRol("soporte");

require_once __DIR__ . "/../app/controlador/prepararDetalleRegistro.php";
require_once __DIR__ . "/../app/vista/detalle_registro.php";
?>