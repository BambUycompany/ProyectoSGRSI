<?php
require_once __DIR__ . "/../config/config.php";

session_start();

require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("administrador");

if (empty($_SESSION["csrfToken"])) {
    $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
}

require_once RUTA_CONTROLADOR . "/prepararDetalleAula.php";
require_once RUTA_VISTA . "/detalle_aula.php";
?>