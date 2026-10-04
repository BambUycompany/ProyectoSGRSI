<?php
// PrestamosController.php

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/PrestamosDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class PrestamosController {
    private PrestamosDAO $dao;

    public function __construct() {
        $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
        $conexion = $conector->establecerConexion();
        
        if ($conexion === null) {
            RespuestaJson::error("Error de conexión a la base de datos", 500);
        }
        $this->dao = new PrestamosDAO($conexion);
    }

    public function gestionar(string $metodo, string $recurso): void {
        session_start();
        $datos = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        if (empty($_SESSION["cedula"])) {
            RespuestaJson::error("No autorizado. Sesión no iniciada.", 401);
        }

        match ($recurso) {
            "prestamo" => $this->gestionarPrestamo($metodo, $datos),
            "portatiles_disponibles" => $this->listarDisponibles($metodo),
            default => RespuestaJson::error("Recurso no encontrado", 404)
        };
    }

    private function gestionarPrestamo(string $metodo, array $datos): void {
        switch ($metodo) {
            case "GET":
                $prestamos = $this->dao->listarPrestamos($_SESSION["cedula"]);
                RespuestaJson::exito(["prestamos" => $prestamos]);
                break;

            case "POST":
                $portatilId = (int)($datos["portatilId"] ?? 0);
                $fechaDev = trim($datos["fechaDev"] ?? "");
                $ciAlumno = trim($datos["ciAlumno"] ?? "");
                $clase = trim($datos["clase"] ?? "");
                $correoAlumno = trim($datos["correoAlumno"] ?? "") ?: null;
                $telefonoAlumno = trim($datos["telefonoAlumno"] ?? "") ?: null;

                if ($portatilId === 0 || empty($fechaDev) || empty($ciAlumno) || empty($clase)) {
                    RespuestaJson::error("Faltan campos obligatorios.", 400);
                }

                $datosPrestamo = [
                    "portatilId" => $portatilId,
                    "fechaDev" => $fechaDev,
                    "ciAlumno" => $ciAlumno,
                    "clase" => $clase,
                    "correoAlumno" => $correoAlumno,
                    "telefonoAlumno" => $telefonoAlumno,
                    "cedula" => $_SESSION["cedula"]
                ];

                $this->dao->registrarPrestamo($datosPrestamo)
                    ? RespuestaJson::exito(["mensaje" => "Préstamo registrado correctamente."], 201)
                    : RespuestaJson::error("Ese portátil ya no está disponible o hubo un error. Elegí otro.", 409);
                break;

            case "PATCH":
                $prestamoId = (int)($datos["prestamoId"] ?? 0);

                if ($prestamoId === 0) {
                    RespuestaJson::error("Préstamo no válido.", 400);
                }

                $this->dao->finalizarPrestamo($prestamoId)
                    ? RespuestaJson::exito(["mensaje" => "Préstamo finalizado correctamente."])
                    : RespuestaJson::error("No se pudo finalizar el préstamo.", 500);
                break;

            default:
                RespuestaJson::error("Método no permitido", 405);
        }
    }

    private function listarDisponibles(string $metodo): void {
        if ($metodo !== "GET") {
            RespuestaJson::error("Método no permitido", 405);
        }
        
        $portatiles = $this->dao->listarPortatilesDisponibles();
        RespuestaJson::exito(["portatiles" => $portatiles]);
    }
}
?>