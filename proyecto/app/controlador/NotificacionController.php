<?php

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/NotificacionDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class NotificacionController {

    private NotificacionDAO $dao;

    public function __construct() {
        $conector = new ConectorPDO(
            $_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"],
            $_ENV["DB_USUARIO"],
            $_ENV["DB_CLAVE"],
            $_ENV["DB_NOMBRE"]
        );

        $conexion = $conector->establecerConexion();

        if ($conexion === null) {
            RespuestaJson::error("No se pudo conectar a la base de datos.", 500);
        }

        $this->dao = new NotificacionDAO($conexion);
    }

    public function gestionar(string $metodo): void {
        $cedula = trim((string) ($_SESSION["cedula"] ?? ""));

        if ($cedula === "") {
            RespuestaJson::error("Tu sesión no está iniciada o ha vencido.", 401);
        }

        if (empty($_SESSION["solicitante"])) {
            RespuestaJson::error("No tenés permiso para consultar estas notificaciones.", 403);
        }

        try {
            if ($metodo === "GET") {
                $this->consultar($cedula);
                return;
            }

            if ($metodo === "PATCH" || $metodo === "PUT") {
                $this->verificarCsrf();
                $this->marcarLeida($cedula);
                return;
            }

            RespuestaJson::error("Método no permitido.", 405);
        } catch (Throwable $error) {
            RespuestaJson::error("Ocurrió un error al gestionar las notificaciones.", 500);
        }
    }

    private function consultar(string $cedula): void {
        $accion = trim((string) ($_GET["accion"] ?? "listar"));

        if ($accion !== "listar") {
            RespuestaJson::error("Acción no encontrada.", 404);
        }

        RespuestaJson::exito([
            "notificaciones" => $this->dao->listarPorDestinatario($cedula),
            "sinLeer" => $this->dao->contarNoLeidas($cedula)
        ]);
    }

    private function marcarLeida(string $cedula): void {
        $accion = trim((string) ($_GET["accion"] ?? ""));

        if ($accion !== "marcarLeida") {
            RespuestaJson::error("Acción no encontrada.", 404);
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("Los datos enviados no son válidos.", 400);
        }

        $id = filter_var(
            $datos["id"] ?? null,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        if ($id === false || $id === null) {
            RespuestaJson::error("La notificación indicada no es válida.", 400);
        }

        if (!$this->dao->marcarComoLeida((int) $id, $cedula)) {
            RespuestaJson::error("No se encontró una notificación de tu usuario con ese identificador.", 404);
        }

        RespuestaJson::exito(["mensaje" => "Notificación marcada como leída."]);
    }

    private function verificarCsrf(): void {
        $recibido = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
        $esperado = $_SESSION["csrfToken"] ?? "";

        if (
            !is_string($recibido) ||
            !is_string($esperado) ||
            $esperado === "" ||
            !hash_equals($esperado, $recibido)
        ) {
            RespuestaJson::error("Token de seguridad inválido.", 403);
        }
    }
}
