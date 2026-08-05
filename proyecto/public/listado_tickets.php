<?php
session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../app/controlador/control_acceso.php";
requerirRol("soporte");

require_once __DIR__ . "/../app/vista/listado_tickets.php";
?>