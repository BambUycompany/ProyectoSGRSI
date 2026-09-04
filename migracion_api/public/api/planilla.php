<?php
require_once __DIR__ . "/../../config/config.php";
require_once RUTA_CONTROLADOR . "/planillaController.php";

session_start();

$controlador = new planillaController();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);