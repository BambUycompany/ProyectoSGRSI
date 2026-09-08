<?php
require_once __DIR__ . "/../../config/config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Método no permitido."));
    exit;
}

$aulaId = (int) ($_POST["aulaId"] ?? 0);
$cantidad = (int) ($_POST["cantidad"] ?? 0);
$modeloPc = trim($_POST["modeloPc"] ?? "");
$monitor = trim($_POST["monitor"] ?? "");
$modeloMouse = trim($_POST["modeloMouse"] ?? "");
$modeloTeclado = trim($_POST["modeloTeclado"] ?? "");

if ($aulaId === 0) {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Aula no válida."));
    exit;
}

if ($cantidad < 1 || $modeloPc === "" || $monitor === "" || $modeloMouse === "" || $modeloTeclado === "") {
    header("Location: ../../public/detalle_aula.php?aulaId=" . $aulaId . "&error=" . urlencode("Faltan datos del equipo."));
    exit;
}

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AltaDatosEquipo.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$altaDatosEquipo = new AltaDatosEquipo($conexion);
$resultado = $altaDatosEquipo->agregarEquipos($aulaId, $cantidad, $modeloPc, $monitor, $modeloMouse, $modeloTeclado);

if (!$resultado) {
    header("Location: ../../public/detalle_aula.php?aulaId=" . $aulaId . "&error=" . urlencode("No se pudieron agregar los equipos."));
    exit;
}

header("Location: ../../public/detalle_aula.php?aulaId=" . $aulaId . "&resultado=" . urlencode("Equipos agregados correctamente."));
exit;
?>