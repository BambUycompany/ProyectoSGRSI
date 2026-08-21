<?php

session_start();


require_once __DIR__ . "/../app/controlador/control_acceso.php";
requerirRol("solicitante");

require_once __DIR__ . "/../app/vista/Solicitante.php";

?>