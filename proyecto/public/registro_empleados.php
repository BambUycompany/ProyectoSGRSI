<?php
session_start();


require_once RUTA_MODELO . "/control_acceso.php";
requerirRol("administrador");

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AltaDatosUsuarios.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$altaDatosUsuarios = new AltaDatosUsuarios($conexion);
$usuarios = $altaDatosUsuarios->listarUsuarios();


if (!isset($_SESSION["csrfToken"])) {
    $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
}
require_once RUTA_VISTA . "/registro_empleados.php";
?>