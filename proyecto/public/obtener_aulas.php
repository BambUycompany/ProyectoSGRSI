<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../app/controlador/prepararObtenerAulas.php";
echo json_encode($aulasPorTipo);
?>