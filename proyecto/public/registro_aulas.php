<?php
session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: login.php");
    exit;
}

$esAdmin = isset($_SESSION["administrador"]) && $_SESSION["administrador"] === true;

if (!$esAdmin) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/../app/vista/registro_aulas.php";
?>