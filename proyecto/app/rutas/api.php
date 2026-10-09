
<?php
require_once RUTA_VISTA . "/RespuestaJson.php";



switch ($ruta) {

    case "login":
        require_once RUTA_CONTROLADOR . "/LoginController.php";
        $controlador = new LoginController();
        $controlador->autenticar($metodo);
        break;

    case "logout":
        require_once RUTA_CONTROLADOR . "/LoginController.php";
        $controlador = new LoginController();
        $controlador->cerrarSesion($metodo);
        break;

    case "usuarios":
        require_once RUTA_CONTROLADOR . "/UsuarioController.php";
        $controlador = new UsuarioController();
        $controlador->gestionar($metodo);
        break;

    case "recursos":
        require_once RUTA_CONTROLADOR . "/GestorRecursosController.php";
        $controlador = new GestorRecursosController();
        $controlador->gestionar($metodo, $recurso);
        break;

    case "planilla":
        require_once RUTA_CONTROLADOR . "/planillaController.php";
        $controlador = new planillaController();
        $controlador->gestionar($metodo);
        break;

    case "tickets":
        require_once RUTA_CONTROLADOR . "/TicketController.php";
        $controlador = new TicketController();
        $controlador->gestionar($metodo);
        break;

    case "prestamos":
        require_once RUTA_CONTROLADOR . "/prestamosController.php";
        $controlador = new PrestamosController();
        $controlador->gestionar($metodo, $recurso);
        break;

    case "metricas":
        require_once RUTA_CONTROLADOR . "/metricasController.php";
        $controlador = new MetricasController();
        $controlador->gestionar($metodo, $recurso);
        break;

    default:
        RespuestaJson::error("Ruta no encontrada.", 404);
}
