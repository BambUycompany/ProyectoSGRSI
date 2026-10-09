<?php
session_start();
require_once __DIR__ . "/../config/config.php";

if (isset($_SESSION["cedula"]) && !isset($_GET["error"])) {
    if (!empty($_SESSION["administrador"])) {
        header("Location: administrador.php");
    } elseif (!empty($_SESSION["soporte"])) {
        header("Location: soporte.php");
    } elseif (!empty($_SESSION["solicitante"])) {
        header("Location: solicitante.php");
    } else {
        header("Location: login.php?error=sinRol");
    }
    exit;
}


require_once RUTA_VISTA . "/login.php";
?>
