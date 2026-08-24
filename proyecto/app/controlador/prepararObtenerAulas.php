<?php
require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDatosPlanilla.php";

$conectorPDO = new ConectorPDO("localhost:3306", "root", "", "SGRSI_db");
$conexion = $conectorPDO->establecerConexion();

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);
$aulas = $accesoDatosPlanilla->listarAulas();

$aulasPorTipo = ["laboratorio" => [], "taller" => []];
foreach ($aulas as $aula) {
    $aulasPorTipo[$aula["Tipo"]][] = $aula["Numero"];
}
?>