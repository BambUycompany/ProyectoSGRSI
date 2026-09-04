<?php
require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosPlanilla.php";


$planillaId = (int) ($_GET["id"] ?? 0);

if ($planillaId === 0) {
    header("Location: ../../public/listado_registros.php");
    exit;
}

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);
$planilla = $accesoDatosPlanilla->obtenerPlanillaPorId($planillaId);

if ($planilla === null) {
    header("Location: ../../public/listado_registros.php?error=" . urlencode("El registro solicitado no existe."));
    exit;
}

$tickets = $accesoDatosPlanilla->listarTicketsDePlanilla($planillaId);
?>