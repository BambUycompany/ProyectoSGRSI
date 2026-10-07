<?php
require_once RUTA_CONTROLADOR . "/LoginController.php";
require_once RUTA_CONTROLADOR . "/UsuarioController.php";
require_once RUTA_CONTROLADOR . "/GestorRecursosController.php";
require_once RUTA_CONTROLADOR . "/planillaController.php";
require_once RUTA_CONTROLADOR . "/TicketController.php";
require_once RUTA_CONTROLADOR . "/prestamosController.php";
require_once RUTA_CONTROLADOR . "/metricasController.php";

switch ($ruta) {
    case "login":
        $controlador = new LoginController();
        $controlador->autenticar($metodo);
        break;

    case "logout":
        $controlador = new LoginController();
        $controlador->cerrarSesion($metodo);
        break;

    case "usuarios":
        $controlador = new UsuarioController();
        $controlador->gestionar($metodo);
        break;

    case "recursos":
        $controlador = new RecursosController();
        $controlador->gestionar($metodo, $recurso);
        break;

    case "planilla":
        $controlador = new planillaController();
        $controlador->gestionar($metodo);
        break;

    case "tickets":
        $controlador = new TicketController();
        $controlador->gestionar($metodo);
        break;

    case "prestamos":
        $controlador = new PrestamosController();
        $controlador->gestionar($metodo, $recurso);
        break;

    case "metricas":
        $controlador = new MetricasController();
        $controlador->gestionar($metodo, $recurso);
        break;

    default:
        RespuestaJson::error("Ruta no encontrada", 404);
}
?>