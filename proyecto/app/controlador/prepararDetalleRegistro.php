<?php
require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDatosPlanilla.php";

$planillaId = (int) ($_GET["id"] ?? 0);

if ($planillaId === 0) {
    header("Location: ../../public/listado_registro.php");
    exit;
}

$conectorPDO = new ConectorPDO("localhost:3306", "root", "", "SGRSI_db");
$conexion = $conectorPDO->establecerConexion();

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);
$planilla = $accesoDatosPlanilla->obtenerPlanillaPorId($planillaId);

if ($planilla === null) {
    header("Location: ../../public/listado_registro.php?error=" . urlencode("El registro solicitado no existe."));
    exit;
}

$tickets = $accesoDatosPlanilla->listarTicketsDePlanilla($planillaId);
?>