<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosMetricas.php";

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
b         break;
}

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosMetricas = new AccesoDatosMetricas($conexion);

$totalTickets = $accesoDatosMetricas->contarTicketsEmitidos($fechaInicio, $fechaFin);
$totalRegistros = $accesoDatosMetricas->contarRegistrosEmitidos($fechaInicio, $fechaFin);
$totalFinalizados = $accesoDatosMetricas->contarTicketsFinalizados($fechaInicio, $fechaFin);
$totalBajoRendimiento = $accesoDatosMetricas->contarPorBajoRendimiento($fechaInicio, $fechaFin);
$pcMasReportada = $accesoDatosMetricas->obtenerPcMasReportada($fechaInicio, $fechaFin);
$pcMasVandalizada = $accesoDatosMetricas->obtenerPcMasVandalizada($fechaInicio, $fechaFin);
$pcsSospechosas = $accesoDatosMetricas->listarPcsSospechosasDeVandalismo(3,$fechaInicio, $fechaFin);
$aulaConMasIncidencias = $accesoDatosMetricas->obtenerAulaConMasIncidencias($fechaInicio, $fechaFin);
$modeloMouseMasDanado = $accesoDatosMetricas->obtenerModeloMouseMasDanado($fechaInicio, $fechaFin);
$modeloTecladoMasDanado = $accesoDatosMetricas->obtenerModeloTecladoMasDanado($fechaInicio, $fechaFin);
?>