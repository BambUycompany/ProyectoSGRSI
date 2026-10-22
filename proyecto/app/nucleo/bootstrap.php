<?php

ini_set("display_errors", "1");
error_reporting(E_ALL);
session_start();
header("Content-Type: application/json; charset=UTF-8");

$recurso = $_GET["recurso"] ?? "";
$identificador = $_GET["identificador"] ?? "";
$metodo = $_SERVER["REQUEST_METHOD"];

require_once RUTA_RUTAS . "/api.php";

?>