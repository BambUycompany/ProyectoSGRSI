<?php

require_once RUTA_MODELO "/PlanillaDAO.php";

class planillaController

{
private function Registro(): void
  

$this->verificarCsrf();
$datos = json_decode(file_get_contents("php://input"), true) ?? [];

    { 
$tipo = trim($datos["tipo"] ?? "");
$numero = trim($datos["numero"] ?? "");
$fecha = trim($datos["fecha"] ?? "");
$horaEntrada = trim($_POST["horaEntrada"] ?? "");
$horaSalida = trim($_POST["horaSalida"] ?? "");
$nombreSolicitante = trim($datos["nombreSolicitante"] ?? "");
$asignatura = trim($datos["Asignatura"] ?? "") ?: null;
$grupo = trim($datos["grupo"] ?? "") ?: null;
$turno = trim($datos["turno"] ?? "") ?: null;
$tickets = $datos["tickets"] ?? []; 
$documentoRegistrante = $datos["cedula"];

if ($tipo === "" || $numero === "" || $fecha === "" || $horaEntrada === "" || $horaSalida === "" || $nombreSolicitante === "") {
    header("Location: ../../public/registro_planilla.php?error=" . urlencode("Faltan campos obligatorios."));
    exit;
}

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);

$aulaId = $dao->buscarAulaId($tipo, $numero);

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

$planillaId = $dao->registrarPlanilla($datosPlanilla);

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


    }


}
?>







