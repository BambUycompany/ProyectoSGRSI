<?php
require_once __DIR__ . "/../../config/config.php";
session_start();

require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("solicitante");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/listado_prestamos.php?error=" . urlencode("Método no permitido."));
    exit;
}

$prestamoId = (int) ($_POST["prestamoId"] ?? 0);

if ($prestamoId === 0) {
    header("Location: ../../public/listado_prestamos.php?error=" . urlencode("Préstamo no válido."));
    exit;
}

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/ModificarDatosPrestamo.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$modificarDatosPrestamo = new ModificarDatosPrestamo($conexion);
$resultado = $modificarDatosPrestamo->finalizarPrestamo($prestamoId);

if (!$resultado) {
    header("Location: ../../public/listado_prestamos.php?error=" . urlencode("No se pudo finalizar el préstamo."));
    exit;
}

header("Location: ../../public/listado_prestamos.php?resultado=" . urlencode("Préstamo finalizado correctamente."));
exit;
?>