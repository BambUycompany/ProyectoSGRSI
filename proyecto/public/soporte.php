<?php

session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: login.php");
    exit;
}

if ( !isset($_SESSION["soporte"]) || $_SESSION["soporte"] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../app/vista/Soporte.php";

?>