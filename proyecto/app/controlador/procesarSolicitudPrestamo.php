<?php
require_once __DIR__ . "/../../config/config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/registro_prestamo.php?error=" . urlencode("Método no permitido."));
    exit;
}

$portatilId = (int) ($_POST["portatilId"] ?? 0);
$fechaDev = trim($_POST["fechaDev"] ?? "");
$ciAlumno = trim($_POST["ciAlumno"] ?? "");
$clase = trim($_POST["clase"] ?? "");
$correoAlumno = trim($_POST["correoAlumno"] ?? "") ?: null;
$telefonoAlumno = trim($_POST["telefonoAlumno"] ?? "") ?: null;

if ($portatilId === 0 || $fechaDev === "" || $ciAlumno === "" || $clase === "") {
    header("Location: ../../public/registro_prestamo.php?error=" . urlencode("Faltan campos obligatorios."));
    exit;
}

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AltaDatosPrestamo.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$altaDatosPrestamo = new AltaDatosPrestamo($conexion);

$resultado = $altaDatosPrestamo->registrarPrestamo([
    "portatilId" => $portatilId,
    "fechaDev" => $fechaDev,
    "ciAlumno" => $ciAlumno,
    "clase" => $clase,
    "correoAlumno" => $correoAlumno,
    "telefonoAlumno" => $telefonoAlumno,
    "cedula" => $_SESSION["cedula"],
]);

if (!$resultado) {
    header("Location: ../../public/registro_prestamo.php?error=" . urlencode("Ese portátil ya no está disponible. Elegí otro."));
    exit;
}

header("Location: ../../public/listado_prestamos.php?resultado=" . urlencode("Préstamo registrado correctamente."));
exit;
?>