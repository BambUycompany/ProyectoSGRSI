<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosAula.php";
require_once RUTA_MODELO . "/AccesoDatosEquipo.php";

$aulaId = (int) ($_GET["aulaId"] ?? 0);

if ($aulaId === 0) {
    header("Location: ../../public/gestor_recursos.php");
    exit;
}

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosAula = new AccesoDatosAula($conexion);
$aula = $accesoDatosAula->obtenerAulaPorId($aulaId);

if ($aula === null) {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("El aula solicitada no existe."));
    exit;
}

$accesoDatosEquipo = new AccesoDatosEquipo($conexion);
$equipos = $accesoDatosEquipo->listarEquiposDeAula($aulaId);
?>