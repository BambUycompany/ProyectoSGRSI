<?php
require_once RUTA_MODELO . "/ConectorPDO";
require_once RUTA_MODELO . "/AulaDAO.php";
require_once RUTA_VISTA . "/RespuestajsonPlanilla.php";

class AulaController{
    
    public function gestionar(string $metodo): void {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJsonAula::error("Acceso denegado: sesión no iniciada", 401);
        }

        match ($metodo) {
            "GET" => $this->listarAulas(),
            "POST" => $this->registrarAula(),
            "PUT" => $this->modificarAula(),
            "DELETE" => $this->eliminarAula(),
            default => RespuestaJsonAula::error("Método no permitido", 405)
        };
    }

    public function listarAulas(): void {
        $conexion = $this->conectar();
        $dao = new AulaDAO($conexion);
        
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $aula = $dao->obtenerAulaPorId((int)$_GET["id"]);
            if (!$aula) {
                RespuestaJsonAula::error("Aula no encontrada", 404);
            }
            RespuestaJsonAula::exito($aula);
        }

        RespuestaJsonAula::exito($dao->listarAulasConDetalle());
    }

    public function registrarAula(): void {
        $this->verificarCsrf();

        $datos = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $tipo = trim($datos["tipo"] ?? "");
        $numero = trim($datos["numero"] ?? "");

        if (empty($tipo) || empty($numero)) {
            RespuestaJsonAula::error("Faltan datos requeridos (tipo y número)", 400);
        }

        $conexion = $this->conectar();
        $dao = new AulaDAO($conexion);

        if ($dao->existeAula($tipo, $numero)) {
            RespuestaJsonAula::error("El " . $tipo . " número " . $numero . " ya existe", 409);
        }

        $aulaId = $dao->crearAula($tipo, $numero);
        if (!$aulaId) {
            RespuestaJsonAula::error("No se pudo registrar el aula", 500);
        }

        RespuestaJsonAula::exito(["mensaje" => "Aula agregada correctamente", "id" => $aulaId], 201);
    }

    public function modificarAula(): void {
        $this->verificarCsrf();

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $aulaId = $datos["aulaId"] ?? $datos["id"] ?? null;
        $tipo = trim($datos["tipo"] ?? "");
        $numero = trim($datos["numero"] ?? "");

        if (!$aulaId || !is_numeric($aulaId) || empty($tipo) || empty($numero)) {
            RespuestaJsonAula::error("Datos insuficientes o inválidos para modificar el aula", 400);
        }

        $conexion = $this->conectar();
        $dao = new AulaDAO($conexion);

        $resultado = $dao->modificarAula((int)$aulaId, $tipo, $numero);
        if (!$resultado) {
            RespuestaJsonAula::error("No se pudo modificar el aula", 500);
        }

        RespuestaJsonAula::exito(["mensaje" => "Aula modificada exitosamente"]);
    }

    public function eliminarAula(): void {
        $this->verificarCsrf();

        $datos = json_decode(file_get_contents("php://input"), true) ?? $_GET;
        $aulaId = $datos["aulaId"] ?? $datos["id"] ?? null;

        if (!$aulaId || !is_numeric($aulaId)) {
            RespuestaJsonAula::error("Identificador de aula no válido", 400);
        }

        $conexion = $this->conectar();
        $dao = new AulaDAO($conexion);

        $resultado = $dao->eliminarAula((int)$aulaId);
        if (!$resultado) {
            RespuestaJsonAula::error("No se pudo eliminar el aula", 500);
        }

        RespuestaJsonAula::exito(["mensaje" => "Aula eliminada exitosamente"]);
    }

    private function verificarCsrf(): void {
        $token = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
        if (!isset($_SESSION["csrfToken"]) || !hash_equals($_SESSION["csrfToken"], $token)) {
            RespuestaJsonAula::error("Solicitud rechazada", 403);
        }
    }

    private function conectar(): PDO {
        $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
        $conexion = $conector->establecerConexion();
        if ($conexion === null) {
            RespuestaJsonAula::error("Error de conexión a la base de datos", 500);
        }
        return $conexion;
    }
}
}