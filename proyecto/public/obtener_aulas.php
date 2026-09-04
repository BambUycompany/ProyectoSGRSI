<?php
require_once __DIR__ . "/../config/config.php";

header("Content-Type: application/json; charset=UTF-8");
require_once RUTA_CONTROLADOR . "/prepararObtenerAulas.php";
echo json_encode($aulasPorTipo);
?>