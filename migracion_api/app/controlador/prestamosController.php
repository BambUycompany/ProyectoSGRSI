<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/prestamosDAO.php";
require_once RUTA_VISTA . "/Respuestajson.php";

class prestamosController
{
    public function gestionar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
            return;
        }

        $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        match ($metodo) {
            'GET' => $id ? $this->obtenerPrestamo($id) : $this->listarDisponibles(),
            'POST' => $this->crearPrestamo(),
            'PUT', 'PATCH' => $this->finalizarPrestamo($id),
            'DELETE' => $this->eliminarPrestamo($id),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }

    private function obtenerPrestamo(int $id): void
    {
        $conexion = $this->conectar();
        $dao = new PrestamoDAO($conexion);
        $prestamo = $dao->obtenerPorId($id);

        if (!$prestamo) {
            RespuestaJson::error("Préstamo no encontrado", 404);
            return;
        }

        if (!$this->esPersonalDeSoporte() && $prestamo['CedulaEstudiante'] !== $_SESSION['cedula']) {
            RespuestaJson::error("Acceso denegado: no tenés permiso para ver este préstamo", 403);
            return;
        }

        echo json_encode(["estado" => "exito", "datos" => $prestamo]);
    }

    private function crearPrestamo(): void
    {
        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        
        $datos['cedula'] = $datos['cedula'] ?? $_SESSION['cedula'];

        if (empty($datos['fecha']) || empty($datos['horaInicio']) || empty($datos['portatilId'])) {
            RespuestaJson::error("Faltan campos obligatorios para registrar el préstamo", 400);
            return;
        }

        $conexion = $this->conectar();
        $dao = new PrestamoDAO($conexion);

        if ($dao->registrarPrestamo($datos)) {
            echo json_encode(["estado" => "exito", "mensaje" => "Préstamo registrado correctamente."]);
        } else {
            RespuestaJson::error("Error al registrar el préstamo", 500);
        }
    }

    private function finalizarPrestamo(?int $id): void
    {
        if (!$id) {
            RespuestaJson::error("Se requiere el ID del préstamo", 400);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $horaFin = $datos['horaFin'] ?? date("H:i:s");

        $conexion = $this->conectar();
        $dao = new PrestamoDAO($conexion);

        $prestamo = $dao->obtenerPorId($id);
        if (!$prestamo) {
            RespuestaJson::error("Préstamo no encontrado", 404);
            return;
        }

        if (!$this->esPersonalDeSoporte() && $prestamo['CedulaEstudiante'] !== $_SESSION['cedula']) {
            RespuestaJson::error("Acceso denegado: no podés finalizar un préstamo ajeno", 403);
            return;
        }

        if ($dao->finalizarPrestamo($id, $horaFin)) {
            echo json_encode(["estado" => "exito", "mensaje" => "Préstamo finalizado correctamente."]);
        } else {
            RespuestaJson::error("No se pudo finalizar el préstamo o ya se encontraba finalizado", 400);
        }
    }

    private function eliminarPrestamo(?int $id): void
    {
        if (!$id) {
            RespuestaJson::error("Se requiere el ID del préstamo a eliminar", 400);
            return;
        }

        $conexion = $this->conectar();
        $dao = new PrestamoDAO($conexion);

        $prestamo = $dao->obtenerPorId($id);
        if (!$prestamo) {
            RespuestaJson::error("Préstamo no encontrado", 404);
            return;
        }

        $cedulaFiltro = $this->esPersonalDeSoporte() ? null : $_SESSION['cedula'];

        if (!$this->esPersonalDeSoporte() && $prestamo['CedulaEstudiante'] !== $_SESSION['cedula']) {
            RespuestaJson::error("Acceso denegado: no podés eliminar un préstamo ajeno", 403);
            return;
        }

        if ($dao->eliminarPrestamo($id, $cedulaFiltro)) {
            echo json_encode(["estado" => "exito", "mensaje" => "Registro de préstamo eliminado."]);
        } else {
            RespuestaJson::error("No se pudo eliminar el préstamo (solo se pueden eliminar préstamos finalizados).", 400);
        }
    }

    private function listarDisponibles(): void
    {
        $conexion = $this->conectar();
        $dao = new PrestamoDAO($conexion);
        echo json_encode(["estado" => "exito", "datos" => $dao->listarPortatilesDisponibles()]);
    }

    private function esPersonalDeSoporte(): bool
    {
        return ($_SESSION['soporte'] ?? false) || ($_SESSION['administrador'] ?? false);
    }

    private function conectar(): PDO
    {
        $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
        $conexion = $conector->establecerConexion();
        if ($conexion === null) {
            RespuestaJson::error("Error de conexión con la base de datos", 500);
            exit;
        }
        return $conexion;
    }
}
?>