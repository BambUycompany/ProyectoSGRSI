<?php
require_once __DIR__ . "/../../config/config.php";
session_start();

require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol(["administrador", "soporte"]);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Método no permitido."));
    exit;
}

$portatilId = (int) ($_POST["portatilId"] ?? 0);    

if ($portatilId === 0) {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Portátil no válido."));
    exit;
}

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/ModificarDatosPortatil.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$modificarDatosPortatil = new ModificarDatosPortatil($conexion);
$resultado = $modificarDatosPortatil->habilitarPortatil($portatilId);

if (!$resultado) {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("No se pudo habilitar (no está disponible)."));
    exit;
}

header("Location: ../../public/gestor_recursos.php?resultado=" . urlencode("Portátil habilitado correctamente."));
exit;
?>