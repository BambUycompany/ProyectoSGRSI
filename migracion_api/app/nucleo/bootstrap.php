<?php

session_start();
header("Content-Type: application/json; charset=UTF-8");

$ruta    = $_GET["ruta"] ?? "";
$recurso = $_GET["recurso"] ?? "";
$metodo  = $_SERVER["REQUEST_METHOD"];

require_once RUTA_RUTAS . "/api.php";

?>