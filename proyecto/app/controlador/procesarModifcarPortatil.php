<?php
require_once __DIR__ . "/../../config/config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Método no permitido."));
    exit;
}

$portatilId = (int) ($_POST["portatilId"] ?? 0);
$modelo = trim($_POST["modeloPortatil"] ?? "");

if ($portatilId === 0 || $modelo === "") {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Datos incompletos."));
    exit;
}

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/ModificarDatosPortatil.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$modificarDatosPortatil = new ModificarDatosPortatil($conexion);
$modificarDatosPortatil->modificarPortatil($portatilId, $modelo);

header("Location: ../../public/gestor_recursos.php?resultado=" . urlencode("Portátil modificado correctamente."));
exit;
?>