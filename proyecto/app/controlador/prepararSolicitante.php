<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosPlanilla.php";

$periodo = $_GET["periodo"] ?? "todo";

$fechaInicio = null;
$fechaFin = null;

switch ($periodo) {
    case "dia":
        $fechaInicio = date("Y-m-d");
        $fechaFin = date("Y-m-d");
        break;
    case "semana":
        $fechaInicio = date("Y-m-d", strtotime("monday this week"));
        $fechaFin = date("Y-m-d");
        break;
    case "mes":
        $fechaInicio = date("Y-m-01");
        $fechaFin = date("Y-m-d");
        break;
    default:
        $periodo = "todo";
        break;
}

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);
$misPlanillas = $accesoDatosPlanilla->listarPlanillasDeUsuario($_SESSION["cedula"], $fechaInicio, $fechaFin);
?>