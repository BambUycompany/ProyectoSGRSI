<?php
session_start();
require_once __DIR__ . "/../config/config.php";

if (!isset($_SESSION["cedula"])) {
    header("Location: login.php");
    exit;
}
require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("administrador");
require_once RUTA_VISTA . "/metricas.php";
?>