<?php

session_start();
if (!isset($_SESSION["cedula"])) {
    header("Location: login.php?error=sinSesion");
    exit;
}

if (!isset($_SESSION["roles"]) || count($_SESSION["roles"]) < 2) {
    header("Location: login.php?error=noAutorizado");
    exit;
}

require_once __DIR__ . "/../app/vista/seleccion_dashboard.php";

?>