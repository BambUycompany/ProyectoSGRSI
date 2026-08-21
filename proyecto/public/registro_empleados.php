<?php
session_start();


require_once __DIR__ . "/../app/controlador/control_acceso.php";
requerirRol("administrador");

require_once __DIR__ . "/../app/modelo/ConectorPDO.php";
require_once __DIR__ . "/../app/modelo/AltaDatosUsuarios.php";

$conectorPDO = new ConectorPDO("localhost:3306", "root", "", "SGRSI_db");
$conexion = $conectorPDO->establecerConexion();

$altaDatosUsuarios = new AltaDatosUsuarios($conexion);
$usuarios = $altaDatosUsuarios->listarUsuarios();


if (!isset($_SESSION["csrfToken"])) {
    $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
}
require_once __DIR__ . "/../app/vista/registro_empleados.php";
?>