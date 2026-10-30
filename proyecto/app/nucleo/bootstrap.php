<?php

ini_set("display_errors", "1");
error_reporting(E_ALL);
session_start();
header("Content-Type: application/json; charset=UTF-8");

$recurso = $_GET["recurso"] ?? "";
$identificador = $_GET["identificador"] ?? "";
$metodo = $_SERVER["REQUEST_METHOD"];

// JWT obligatorio para las operaciones de Gestión de Empleados.
if ($recurso === "usuarios") {
    require_once RUTA_NUCLEO . "/TokenJWT.php";
    require_once RUTA_VISTA . "/RespuestaJson.php";

    $autorizacion = $_SERVER["HTTP_AUTHORIZATION"]
        ?? $_SERVER["REDIRECT_HTTP_AUTHORIZATION"]
        ?? "";

    if ($autorizacion === "" && function_exists("getallheaders")) {
        foreach (getallheaders() as $nombre => $valor) {
            if (strcasecmp($nombre, "Authorization") === 0) {
                $autorizacion = $valor;
                break;
            }
        }
    }

    if (!is_string($autorizacion) ||
        !preg_match('/^Bearer\s+([^\s]+)$/i', trim($autorizacion), $coincidencias)) {
        RespuestaJson::error("Falta el token de autenticación.", 401);
    }

    $datosToken = TokenJWT::validar($coincidencias[1]);

    if ($datosToken === null ||
        empty($_SESSION["cedula"]) ||
        ($datosToken["sub"] ?? null) !== (string) $_SESSION["cedula"]) {
        RespuestaJson::error("Token inválido o vencido.", 401);
    }
}

require_once RUTA_RUTAS . "/api.php";

?>