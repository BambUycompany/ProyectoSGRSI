<?php
require_once __DIR__ . "/../../config/config.php";
require_once RUTA_CONTROLADOR . "/AulaController.php";

session_start();

$controlador = new AulaController();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);