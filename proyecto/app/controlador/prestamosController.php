
<?php

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/prestamosDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class PrestamosController {

    private PrestamoDAO $dao;

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
                "Error de conexión a la base de datos.",
                500
            );
        }

        $this->dao = new PrestamoDAO($conexion);
    }

    public function gestionar(
        string $metodo,
        string $recurso
    ): void {

        if (empty($_SESSION["cedula"])) {
            RespuestaJson::error(
                "Debe iniciar sesión.",
                401
            );
        }

        $esSolicitante = !empty($_SESSION["solicitante"]);
        $esSoporte = !empty($_SESSION["soporte"]);

        if (!$esSolicitante && !$esSoporte) {
            RespuestaJson::error(
                "No tiene permisos para acceder a préstamos.",
                403
            );
        }

        if ($metodo !== "GET") {

            $token = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
            $tokenSesion = $_SESSION["csrfToken"] ?? "";

            if (
                !is_string($token) ||
                !is_string($tokenSesion) ||
                $tokenSesion === "" ||
                !hash_equals($tokenSesion, $token)
            ) {
                RespuestaJson::error(
                    "Token de seguridad inválido.",
                    403
                );
            }
        }

        $datos = [];

        if ($metodo !== "GET") {

            $contenido = file_get_contents("php://input");

            $datos = json_decode($contenido ?: "{}", true);

            if (!is_array($datos) || array_is_list($datos)) {
                RespuestaJson::error(
                    "Los datos enviados no son válidos.",
                    400
                );
            }
        }

        switch ($recurso) {

            case "portatiles_disponibles":

                if ($metodo !== "GET" || !$esSolicitante) {
                    RespuestaJson::error(
                        "Operación no permitida.",
                        403
                    );
                }

                RespuestaJson::exito(
                    $this->dao->listarPortatilesDisponibles()
                );
                break;

            case "prestamo":

                switch ($metodo) {

                    case "GET":
                        $this->listarPrestamos(
                            $esSolicitante,
                            $esSoporte
                        );
                        break;

                    case "POST":

                        if (!$esSolicitante) {
                            RespuestaJson::error(
                                "Solo un docente puede solicitar préstamos.",
                                403
                            );
                        }

                        $this->registrarPrestamo($datos);
                        break;

                    case "PATCH":

                        if (!$esSoporte) {
                            RespuestaJson::error(
                                "Solo soporte puede finalizar préstamos.",
                                403
                            );
                        }

                        $this->finalizarPrestamo($datos);
                        break;

                    default:
                        RespuestaJson::error(
                            "Método no permitido.",
                            405
                        );
                }
                break;

            default:
                RespuestaJson::error(
                    "Recurso no encontrado.",
                    404
                );
        }
    }

    private function listarPrestamos(
        bool $esSolicitante,
        bool $esSoporte
    ): void {

     
        $vista = $_GET["vista"] ?? "propios";

        if (!in_array($vista, ["propios", "todos"], true)) {
            RespuestaJson::error(
                "Vista de préstamos no válida.",
                400
            );
        }

        if ($vista === "todos") {

            if (!$esSoporte) {
                RespuestaJson::error(
                    "No tiene permiso para consultar todos los préstamos.",
                    403
                );
            }

            $prestamos = $this->dao->listarTodosPrestamos();

        } else {

            if (!$esSolicitante) {
                RespuestaJson::error(
                    "No tiene rol solicitante.",
                    403
                );
            }

            $prestamos = $this->dao->listarPrestamos(
                $_SESSION["cedula"]
            );
        }

        RespuestaJson::exito($prestamos);
    }

    private function registrarPrestamo(array $datos): void {

        $portatilId = filter_var(
            $datos["portatilId"] ?? null,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        $ciAlumno = trim($datos["ciAlumno"] ?? "");
        $clase = trim($datos["clase"] ?? "");
        $correo = trim($datos["correoAlumno"] ?? "");
        $telefono = trim($datos["telefonoAlumno"] ?? "");

        if (
            !$portatilId ||
            !preg_match('/^[0-9]{8}$/', $ciAlumno) ||
            $clase === ""
        ) {
            RespuestaJson::error(
                "Complete correctamente el portátil, " .
                "la cédula del alumno y el grupo.",
                400
            );
        }

        if (
            strlen($clase) > 50 ||
            strlen($correo) > 150 ||
            strlen($telefono) > 20
        ) {
            RespuestaJson::error(
                "Uno de los campos supera el largo permitido.",
                400
            );
        }

        if (
            $correo !== "" &&
            !filter_var($correo, FILTER_VALIDATE_EMAIL)
        ) {
            RespuestaJson::error(
                "Correo electrónico inválido.",
                400
            );
        }

        
        $datosPrestamo = [
            "portatilId" => (int)$portatilId,
            "ciAlumno" => $ciAlumno,
            "clase" => $clase,
            "correoAlumno" => $correo ?: null,
            "telefonoAlumno" => $telefono ?: null,
            "cedula" => $_SESSION["cedula"]
        ];

        if (!$this->dao->registrarPrestamo($datosPrestamo)) {
            RespuestaJson::error(
                "No se pudo registrar el préstamo. " .
                "Verifique que el portátil esté disponible.",
                409
            );
        }

        RespuestaJson::exito([
            "mensaje" => "Préstamo registrado correctamente."
        ], 201);
    }

    private function finalizarPrestamo(array $datos): void {

        $prestamoId = filter_var(
            $datos["prestamoId"] ?? null,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        if (!$prestamoId) {
            RespuestaJson::error(
                "Identificador de préstamo inválido.",
                400
            );
        }

        if (!$this->dao->finalizarPrestamo((int)$prestamoId)) {
            RespuestaJson::error(
                "No se pudo finalizar el préstamo. " .
                "Puede que ya esté finalizado.",
                409
            );
        }

        RespuestaJson::exito([
            "mensaje" => "Devolución registrada correctamente."
        ]);
    }
}
