<?php
session_start();

require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDatosPlanilla.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/registro_planilla.php?error=" . urlencode("Método no permitido."));
    exit;
}
$tipo = trim($_POST["tipo"] ?? "");
$numero = trim($_POST["numero"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$horaEntrada = trim($_POST["horaEntrada"] ?? "");
$horaSalida = trim($_POST["horaSalida"] ?? "");
$nombreSolicitante = trim($_POST["nombreSolicitante"] ?? "");
$asignatura = trim($_POST["Asignatura"] ?? "") ?: null;
$grupo = trim($_POST["grupo"] ?? "") ?: null;
$turno = trim($_POST["turno"] ?? "") ?: null;

$tickets = $_POST["tickets"] ?? []; 

$documentoRegistrante = $_SESSION["cedula"];

if ($tipo === "" || $numero === "" || $fecha === "" || $horaEntrada === "" || $horaSalida === "" || $nombreSolicitante === "") {
    header("Location: ../../public/registro_planilla.php?error=" . urlencode("Faltan campos obligatorios."));
    exit;
}
$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);

$aulaId = $accesoDatosPlanilla->buscarAulaId($tipo, $numero);

if ($aulaId === null) {
    header("Location: ../../public/registro_planilla.php?error=" . urlencode("El aula seleccionada no existe."));
    exit;
}
$datosPlanilla = [
    "fecha" => $fecha,
    "horaEntrada" => $horaEntrada,
    "horaSalida" => $horaSalida,
    "grupo" => $grupo,
    "turno" => $turno,
    "asignatura" => $asignatura,
    "nombreSolicitante" => $nombreSolicitante,
    "documentoRegistrante" => $documentoRegistrante,
    "aulaId" => $aulaId,
];

$planillaId = $accesoDatosPlanilla->registrarPlanilla($datosPlanilla);

foreach ($tickets as $ticket) {
    $datosTicket = [
        "numeroPc" => $ticket["numeroPc"] ?? "",
        "fallo" => $ticket["fallo"] ?? "",
        "descripcion" => $ticket["descripcion"] ?? "",
        "aulaId" => $aulaId,
        "documentoRegistrante" => $documentoRegistrante,
        "planillaId" => $planillaId,
    ];

    $accesoDatosPlanilla->registrarTicket($datosTicket);
}

header("Location: ../../public/registro_planilla.php?resultado=" . urlencode("Registro guardado correctamente."));
exit;
?>