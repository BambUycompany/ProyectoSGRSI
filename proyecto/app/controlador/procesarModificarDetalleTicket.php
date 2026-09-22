<?php
require_once __DIR__ . "/../../config/config.php";
session_start();

require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("soporte");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/listado_tickets.php?error=" . urlencode("Método no permitido."));
    exit;
}

$ticketId = (int) ($_POST['ticket_id'] ?? 0);
$accion = trim($_POST['accion'] ?? '');
$pc = trim($_POST['pc'] ?? '');
$aulaId = (int) ($_POST['aulaId'] ?? 0);

$urlRedireccion = "../../public/detalle_tickets.php?pc=" . urlencode($pc) . "&aulaId=" . $aulaId;

if ($ticketId === 0 || $accion === "") {
    header("Location: " . $urlRedireccion . "&error=" . urlencode("Faltan datos obligatorios."));
    exit;
}

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/ModificarDatosTickets.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$modificarDatosTickets = new ModificarDatosTickets($conexion);
$resultado = false;

if ($accion === 'cambiar_prioridad') {
    $prioridad = trim($_POST['prioridad'] ?? '');
    $resultado = $modificarDatosTickets->cambiarPrioridadTicket($ticketId, $prioridad);
} elseif ($accion === 'cambiar_estado') {
    $estado = trim($_POST['estado'] ?? '');
    $resultado = $modificarDatosTickets->cambiarEstadoTicket($ticketId, $estado);
} elseif ($accion === 'finalizar_ticket') {
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $soporteCedula = $_SESSION['cedula'] ?? '';

    if ($diagnostico === '' || $soporteCedula === '') {
        header("Location: " . $urlRedireccion . "&error=" . urlencode("El diagnóstico y la cédula de soporte son obligatorios."));
        exit;
    }

    $resultado = $modificarDatosTickets->finalizarTicket($ticketId, $diagnostico, $soporteCedula);
}

if (!$resultado) {
    header("Location: " . $urlRedireccion . "&error=" . urlencode("No se pudo realizar la modificación del ticket."));
    exit;
}

header("Location: " . $urlRedireccion . "&resultado=" . urlencode("Ticket actualizado correctamente."));
exit;