<?php
session_start();

require_once __DIR__ . "/../app/controlador/control_acceso.php";
requerirRol("soporte");

require_once __DIR__ . "/../app/modelo/ConectorPDO.php";
require_once __DIR__ . "/../app/modelo/AccesoDatosPlanilla.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$planillaId = (int) ($_GET["id"] ?? 0);

if ($planillaId === 0) {
    header("Location: listado_registro.php");
    exit;
}

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);

$planilla = $accesoDatosPlanilla->obtenerPlanillaPorId($planillaId);

if ($planilla === null) {
    header("Location: listado_registros.php?error=" . urlencode("El registro solicitado no existe."));
    exit;
}

$tickets = $accesoDatosPlanilla->listarTicketsDePlanilla($planillaId);

require_once __DIR__ . "/../app/vista/detalle_registros.php";
?>