
<?php
session_start();

require_once __DIR__ . "/../app/controlador/control_acceso.php";
requerirRol(["administrador", "soporte", "solicitante"]);

require_once __DIR__ . "/../app/modelo/ConectorPDO.php";
require_once __DIR__ . "/../app/modelo/AccesoDatosPlanilla.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);
$aulas = $accesoDatosPlanilla->listarAulas();

require_once __DIR__ . "/../app/vista/registro_planilla.php";
?>