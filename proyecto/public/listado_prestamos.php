<?php
require_once __DIR__ . "/../config/config.php";
session_start();

require_once RUTA_CONTROLADOR . "/control_acceso.php";
requerirRol("solicitante");

if (empty($_SESSION["csrfToken"])) {
    $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
}

require_once RUTA_CONTROLADOR . "/prepararListadoPrestamos.php";
require_once RUTA_VISTA . "/listado_prestamos.php";
?>