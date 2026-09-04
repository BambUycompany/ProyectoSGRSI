<?php
require_once __DIR__ . "/../../config/config.php";
$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$altaDatosUsuarios = new AltaDatosUsuarios($conexion);
$usuarios = $altaDatosUsuarios->listarUsuarios();


if (!isset($_SESSION["csrfToken"])) {
    $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
}
?>