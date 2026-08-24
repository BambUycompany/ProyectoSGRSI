<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../app/modelo/ConectorPDO.php";
require_once __DIR__ . "/../app/modelo/AccesoDatosPlanilla.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);
$aulas = $accesoDatosPlanilla->listarAulas();

$aulasPorTipo = ["laboratorio" => [], "taller" => []];
foreach ($aulas as $aula) {
    $aulasPorTipo[$aula["Tipo"]][] = $aula["Numero"];
}

echo json_encode($aulasPorTipo);
?>