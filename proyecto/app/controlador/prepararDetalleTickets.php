
<?php
require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosTickets.php";

$numPc = trim($_GET["pc"] ?? "");
$aulaId = (int) ($_GET["aulaId"] ?? 0);
$aulaTipo = trim($_GET["AulaTipo"] ?? "");
$aulaNumero = trim($_GET["AulaNumero"] ?? "");

if ($numPc === "" || $aulaId === 0) {
    header("Location: ../../public/listado_tickets.php");
    exit;
}

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosTickets = new AccesoDatosTickets($conexion);
$tickets = $accesoDatosTickets->listarTicketsPorPcYAula($numPc, $aulaId);
?>