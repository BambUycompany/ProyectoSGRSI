<?php
session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: login.php");
    exit;
}

$esAdmin   = isset($_SESSION["administrador"]) && $_SESSION["administrador"] === true;
$esSoporte = isset($_SESSION["soporte"])       && $_SESSION["soporte"]       === true;

if (!$esAdmin && !$esSoporte) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/../app/vista/registro_empleados.php";
?>