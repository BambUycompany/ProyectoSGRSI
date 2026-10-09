<?php
session_start();

$rol = $_GET["rol"] ?? "";
//importantisimo el in_array para que nadie se autoponga algun rol desde la url
if (!isset($_SESSION["cedula"]) || !in_array($rol, $_SESSION["roles"])) {
    header("Location: ../../public/login.php?error=NoAutorizado");
    exit;
}

$_SESSION["rolActivo"] = $rol;

$destinos = [
    "administrador" => "../../public/administrador.php",
    "soporte"       => "../../public/soporte.php",
    "solicitante"   => "../../public/solicitante.php",
];

header("Location:" . $destinos[$rol]);
exit;
?>