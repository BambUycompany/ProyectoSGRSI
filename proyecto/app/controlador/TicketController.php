
<?php

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/TicketDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class TicketController {

    private TicketDAO $dao;

    public function __construct() {

        $conector = new ConectorPDO(
            $_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"],
            $_ENV["DB_USUARIO"],
            $_ENV["DB_CLAVE"],
            $_ENV["DB_NOMBRE"]
        );

        $conexion = $conector->establecerConexion();

        if ($conexion === null) {
            RespuestaJson::error(
                "No se pudo conectar a la base de datos.",
                500
            );
        }

        $this->dao = new TicketDAO($conexion);
    }

    public function gestionar(): void {

        if (empty($_SESSION["cedula"])) {
            RespuestaJson::error(
                "Tu sesión no está iniciada o ha vencido.",
                401
            );
        }

        if (empty($_SESSION["soporte"])) {
            RespuestaJson::error(
                "No tenés permiso para gestionar tickets.",
                403
            );
        }

        $metodo = $_SERVER["REQUEST_METHOD"] ?? "GET";
        $accion = $_GET["accion"] ?? "";

        try {

            switch ($metodo) {

                case "GET":
                    $this->gestionarConsulta($accion);
                    break;

                case "PUT":
                    $this->verificarCsrf();

                    $datos = $this->obtenerDatos();

                    $this->gestionarModificacion(
                        $accion,
                        $datos
                    );
                    break;

                default:
                    RespuestaJson::error(
                        "Método no permitido.",
                        405
                    );
            }

        } catch (Throwable $error) {

            error_log(
                "Error en TicketController: " .
                $error->getMessage()
            );

            RespuestaJson::error(
                "Ocurrió un error al procesar la solicitud.",
                500
            );
        }
    }

    private function verificarCsrf(): void {

        $tokenRecibido = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
        $tokenSesion = $_SESSION["csrfToken"] ?? "";

        if (
            !is_string($tokenRecibido) ||
            !is_string($tokenSesion) ||
            $tokenSesion === "" ||
            !hash_equals($tokenSesion, $tokenRecibido)
        ) {
            RespuestaJson::error(
                "Token de seguridad inválido.",
                403
            );
        }
    }

    private function obtenerDatos(): array {

        $contenido = file_get_contents("php://input");

        $datos = json_decode(
            $contenido ?: "",
            true
        );

        if (
            !is_array($datos) ||
            array_is_list($datos)
        ) {
            RespuestaJson::error(
                "Los datos enviados no son válidos.",
                400
            );
        }

        return $datos;
    }

    private function gestionarConsulta(
        string $accion
    ): void {

        switch ($accion) {

            case "listarAgrupados":
                $this->listarAgrupados();
                break;

            case "listarPorPCAULA":
                $this->listarPorPCAULA();
                break;

            default:
                RespuestaJson::error(
                    "Acción de consulta no permitida.",
                    405
                );
        }
    }

    private function gestionarModificacion(
        string $accion,
        array $datos
    ): void {

        switch ($accion) {

            case "cambiarEstado":
                $this->cambiarEstado($datos);
                break;

            case "cambiarPrioridad":
                $this->cambiarPrioridad($datos);
                break;

            case "finalizar":
                $this->finalizar($datos);
                break;

            default:
                RespuestaJson::error(
                    "Acción de modificación no permitida.",
                    405
                );
        }
    }

    private function validarIdTicket(
        array $datos
    ): int {

        $id = filter_var(
            $datos["id"] ?? null,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 1
                ]
            ]
        );

        if ($id === false || $id === null) {
            RespuestaJson::error(
                "El identificador del ticket no es válido.",
                400
            );
        }

        return (int) $id;
    }

    private function obtenerTicketExistente(
        int $id
    ): array {

        $ticket = $this->dao->obtenerTicketPorId($id);

        if ($ticket === null) {
            RespuestaJson::error(
                "El ticket solicitado no existe.",
                404
            );
        }

        return $ticket;
    }

    private function verificarTicketEditable(
        array $ticket
    ): void {

        if (
            strtolower(trim($ticket["Estado"])) === "finalizado"
        ) {
            RespuestaJson::error(
                "El ticket ya está finalizado y no puede modificarse.",
                409
            );
        }
    }

    private function listarAgrupados(): void {

        $tickets = $this->dao->listarTicketsAgrupados();

        RespuestaJson::exito([
            "tickets" => $tickets
        ]);
    }

    private function listarPorPCAULA(): void {

        $numeroPc = trim($_GET["pc"] ?? "");

        $aulaId = filter_var(
            $_GET["aulaId"] ?? null,
            FILTER_VALIDATE_INT,
            [
                "options" => [
                    "min_range" => 1
                ]
            ]
        );

        if (
            $numeroPc === "" ||
            $aulaId === false ||
            $aulaId === null
        ) {
            RespuestaJson::error(
                "Debe indicar una PC y un aula válidas.",
                400
            );
        }

        $tickets = $this->dao->listarTicketsPorPcYAula(
            $numeroPc,
            (int) $aulaId
        );

        RespuestaJson::exito([
            "pc" => $numeroPc,
            "aulaId" => (int) $aulaId,
            "tickets" => $tickets
        ]);
    }

    private function cambiarEstado(
        array $datos
    ): void {

        $id = $this->validarIdTicket($datos);

        $nuevoEstado = strtolower(
            trim($datos["nuevoEstado"] ?? "")
        );

        $estadosPermitidos = [
            "pendiente",
            "en proceso"
        ];

        if (
            !in_array(
                $nuevoEstado,
                $estadosPermitidos,
                true
            )
        ) {
            RespuestaJson::error(
                "El estado seleccionado no es válido.",
                400
            );
        }

        $ticket = $this->obtenerTicketExistente($id);

        $this->verificarTicketEditable($ticket);

        $resultado = $this->dao->cambiarEstadoTicket(
            $id,
            $nuevoEstado
        );

        if (!$resultado) {
            RespuestaJson::error(
                "No se pudo cambiar el estado del ticket.",
                409
            );
        }

        RespuestaJson::exito([
            "mensaje" => "Estado del ticket actualizado correctamente."
        ]);
    }

    private function cambiarPrioridad(
        array $datos
    ): void {

        $id = $this->validarIdTicket($datos);

        $nuevaPrioridad = strtolower(
            trim($datos["nuevaPrioridad"] ?? "")
        );

        $prioridadesPermitidas = [
            "baja",
            "media",
            "alta"
        ];

        if (
            !in_array(
                $nuevaPrioridad,
                $prioridadesPermitidas,
                true
            )
        ) {
            RespuestaJson::error(
                "La prioridad seleccionada no es válida.",
                400
            );
        }

        $ticket = $this->obtenerTicketExistente($id);

        $this->verificarTicketEditable($ticket);

        $resultado = $this->dao->cambiarPrioridadTicket(
            $id,
            $nuevaPrioridad
        );

        if (!$resultado) {
            RespuestaJson::error(
                "No se pudo actualizar la prioridad del ticket.",
                409
            );
        }

        RespuestaJson::exito([
            "mensaje" => "Prioridad del ticket actualizada correctamente."
        ]);
    }

    private function finalizar(
        array $datos
    ): void {

        $id = $this->validarIdTicket($datos);

        $diagnostico = trim(
            $datos["diagnostico"] ?? ""
        );

        if ($diagnostico === "") {
            RespuestaJson::error(
                "Debe escribir un diagnóstico para finalizar el ticket.",
                400
            );
        }

        $ticket = $this->obtenerTicketExistente($id);

        $this->verificarTicketEditable($ticket);

        $soporteCedula = $_SESSION["cedula"];

        $resultado = $this->dao->finalizarTicket(
            $id,
            $diagnostico,
            $soporteCedula
        );

        if (!$resultado) {
            RespuestaJson::error(
                "No se pudo finalizar el ticket.",
                409
            );
        }

        RespuestaJson::exito([
            "mensaje" => "Ticket finalizado correctamente.",
            "id" => $id,
            "estado" => "finalizado"
        ]);
    }
}


    

