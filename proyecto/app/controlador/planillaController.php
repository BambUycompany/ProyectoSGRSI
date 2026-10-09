
<?php

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/PlanillaDAO.php";
require_once RUTA_MODELO . "/TicketDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class planillaController {

    private PDO $conexion;
    private PlanillaDAO $planillaDAO;
    private TicketDAO $ticketDAO;

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

      
        $this->conexion = $conexion;
        $this->planillaDAO = new PlanillaDAO($conexion);
        $this->ticketDAO = new TicketDAO($conexion);
    }

    public function gestionar(string $metodo): void {

        if (empty($_SESSION["cedula"])) {
            RespuestaJson::error(
                "Debe iniciar sesión.",
                401
            );
        }

        $autorizado =
            !empty($_SESSION["administrador"]) ||
            !empty($_SESSION["soporte"]) ||
            !empty($_SESSION["solicitante"]);

        if (!$autorizado) {
            RespuestaJson::error("Acceso denegado.", 403);
        }

        switch ($metodo) {
            case "GET":
                $this->consultar();
                break;

            case "POST":
                $this->verificarCsrf();
                $this->registrar();
                break;

            default:
                RespuestaJson::error(
                    "Método no permitido.",
                    405
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

    private function consultar(): void {

        $cedula = $_SESSION["cedula"];

        
        $soloPropios =
            !empty($_SESSION["solicitante"]) &&
            empty($_SESSION["administrador"]) &&
            empty($_SESSION["soporte"]);

        if (isset($_GET["id"])) {

            $id = filter_var(
                $_GET["id"],
                FILTER_VALIDATE_INT,
                ["options" => ["min_range" => 1]]
            );

            if ($id === false) {
                RespuestaJson::error(
                    "ID de planilla inválido.",
                    400
                );
            }

            $planilla = $this->planillaDAO->obtenerPlanillaPorId(
                $id,
                $soloPropios ? $cedula : null
            );

            if ($planilla === null) {
                RespuestaJson::error(
                    "Planilla no encontrada o sin permiso.",
                    404
                );
            }

            $tickets = $this->ticketDAO
                ->listarTicketsDePlanilla($id);

            RespuestaJson::exito([
                "planilla" => $planilla,
                "tickets" => $tickets
            ]);
        }

        $periodo = trim($_GET["periodo"] ?? "todo");

        $periodosPermitidos = [
            "dia", "semana", "mes", "todo"
        ];

        if (!in_array($periodo, $periodosPermitidos, true)) {
            RespuestaJson::error(
                "Período inválido.",
                400
            );
        }

        $planillas = $this->planillaDAO->listarRegistroPlanilla(
            $soloPropios ? $cedula : null,
            $periodo
        );

        RespuestaJson::exito([
            "planillas" => $planillas
        ]);
    }

    private function registrar(): void {

        $contenido = file_get_contents("php://input");
        $datos = json_decode($contenido ?: "", true);

        if (
            !is_array($datos) ||
            array_is_list($datos)
        ) {
            RespuestaJson::error(
                "El cuerpo debe contener un objeto JSON válido.",
                400
            );
        }

        $tipo = strtolower(trim($datos["tipo"] ?? ""));
        $numero = trim($datos["numero"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $horaEntrada = trim($datos["horaEntrada"] ?? "");
        $horaSalida = trim($datos["horaSalida"] ?? "");
        $nombreSolicitante = trim(
            $datos["nombreSolicitante"] ?? ""
        );
        $asignatura = trim($datos["Asignatura"] ?? "");
        $grupo = trim($datos["grupo"] ?? "");
        $turno = trim($datos["turno"] ?? "");
        $tickets = $datos["tickets"] ?? [];

        $cedula = $_SESSION["cedula"];

        if (
            !in_array($tipo, ["laboratorio", "taller"], true) ||
            $numero === "" ||
            $nombreSolicitante === "" ||
            $fecha === "" ||
            $horaEntrada === "" ||
            $horaSalida === ""
        ) {
            RespuestaJson::error(
                "Faltan campos obligatorios.",
                400
            );
        }

        $fechaObjeto = DateTimeImmutable::createFromFormat(
            "!Y-m-d",
            $fecha
        );

        if (
            !$fechaObjeto ||
            $fechaObjeto->format("Y-m-d") !== $fecha
        ) {
            RespuestaJson::error(
                "La fecha ingresada no es válida.",
                400
            );
        }

        foreach ([$horaEntrada, $horaSalida] as $hora) {
            if (
                !preg_match(
                    '/^([01][0-9]|2[0-3]):[0-5][0-9]$/',
                    $hora
                )
            ) {
                RespuestaJson::error(
                    "Formato de hora inválido.",
                    400
                );
            }
        }

        
        if ($horaSalida <= $horaEntrada) {
            RespuestaJson::error(
                "La hora de salida debe ser posterior a la entrada.",
                400
            );
        }

        if (
            mb_strlen($nombreSolicitante) > 150 ||
            mb_strlen($asignatura) > 100 ||
            mb_strlen($grupo) > 50 ||
            mb_strlen($turno) > 30 ||
            mb_strlen($numero) > 20
        ) {
            RespuestaJson::error(
                "Alguno de los campos supera el largo permitido.",
                400
            );
        }

        if (!is_array($tickets) || !array_is_list($tickets)) {
            RespuestaJson::error(
                "El listado de tickets es inválido.",
                400
            );
        }

        if (count($tickets) > 50) {
            RespuestaJson::error(
                "No se permiten más de 50 tickets por planilla.",
                400
            );
        }

        if (
            count($tickets) > 0 &&
            empty($_SESSION["solicitante"])
        ) {
            RespuestaJson::error(
                "Para reportar tickets, el usuario debe tener rol solicitante.",
                403
            );
        }

        $fallosPermitidos = [
            "falta_mouse",
            "mouse_no_funciona",
            "falta_teclado",
            "teclado_no_funciona",
            "no_prende",
            "sin_almacenamiento",
            "bajo_rendimiento",
            "otro"
        ];

        foreach ($tickets as $ticket) {

            if (!is_array($ticket)) {
                RespuestaJson::error(
                    "Ticket inválido.",
                    400
                );
            }

            $numeroPc = trim($ticket["numeroPc"] ?? "");
            $fallo = trim($ticket["fallo"] ?? "");
            $descripcion = trim($ticket["descripcion"] ?? "");

            if (
                !preg_match('/^PC-[0-9]{2,}$/', $numeroPc) ||
                !in_array($fallo, $fallosPermitidos, true) ||
                $descripcion === ""
            ) {
                RespuestaJson::error(
                    "Hay tickets con datos incompletos o inválidos.",
                    400
                );
            }
        }

        try {

            
            $this->conexion->beginTransaction();

            $aulaId = $this->planillaDAO->buscarAulaId(
                $tipo,
                $numero
            );

            if ($aulaId === null) {
                $this->conexion->rollBack();
                RespuestaJson::error(
                    "El aula seleccionada no existe.",
                    404
                );
            }

            foreach ($tickets as $ticket) {
                if (
                    !$this->planillaDAO->existePcEnAula(
                        trim($ticket["numeroPc"]),
                        $aulaId
                    )
                ) {
                    $this->conexion->rollBack();

                    RespuestaJson::error(
                        "La PC " . $ticket["numeroPc"] .
                        " no pertenece al aula seleccionada.",
                        400
                    );
                }
            }

            $datosPlanilla = [
                "fecha" => $fecha,
                "horaEntrada" => $horaEntrada,
                "horaSalida" => $horaSalida,
                "grupo" => $grupo ?: null,
                "turno" => $turno ?: null,
                "asignatura" => $asignatura ?: null,
                "nombreSolicitante" => $nombreSolicitante,
                "documentoRegistrante" => $cedula,
                "aulaId" => $aulaId
            ];

            $planillaId = $this->planillaDAO
                ->registrarPlanilla($datosPlanilla);

            foreach ($tickets as $ticket) {

                $datosTicket = [
                    "numeroPc" => trim($ticket["numeroPc"]),
                    "fallo" => trim($ticket["fallo"]),
                    "descripcion" => trim(
                        $ticket["descripcion"]
                    ),
                    "aulaId" => $aulaId,
                    "documentoRegistrante" => $cedula,
                    "planillaId" => $planillaId
                ];

                $this->ticketDAO->registrarTicket(
                    $datosTicket
                );
            }

            $this->conexion->commit();

            RespuestaJson::exito([
                "mensaje" => "Registro guardado correctamente.",
                "planillaId" => $planillaId,
                "cantidadTickets" => count($tickets)
            ], 201);

        } catch (Throwable $error) {

         
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log(
                "Error al registrar planilla: " .
                $error->getMessage()
            );

            RespuestaJson::error(
                "No se pudo guardar la planilla ni sus tickets.",
                500
            );
        }
    }
}








