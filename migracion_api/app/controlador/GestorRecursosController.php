<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/RecursosDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class RecursosController {
    private RecursosDAO $dao;

    public function __construct() {
        $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
        $conexion = $conector->establecerConexion();
        
        if ($conexion === null) {
            RespuestaJson::error("Error de conexión a la base de datos", 500);
        }
        $this->dao = new RecursosDAO($conexion);
    }

    
    public function gestionar(string $metodo, string $recurso): void {
    
        $datos = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        match ($recurso) {
            "aula" => $this->gestionarAula($metodo, $datos),
            "equipo" => $this->gestionarEquipo($metodo, $datos),
            "portatil" => $this->gestionarPortatil($metodo, $datos),
            "planilla" => $this->gestionarPlanillas($metodo),
            default => RespuestaJson::error("Recurso no encontrado", 404)
        };
    }

    private function gestionarAula(string $metodo, array $datos): void {
        switch ($metodo) {
            case "GET":
                $id = (int)($_GET["aulaId"] ?? 0);
                if ($id > 0) {
                    $aula = $this->dao->obtenerAulaPorId($id);
                    if ($aula === null) {
                        RespuestaJson::error("El aula solicitada no existe.", 404);
                        return exit();
                    }
                    $equipos = $this->dao->listarEquiposDeAula($id);
                    RespuestaJson::exito(["aula" => $aula, "equipos" => $equipos]);
                } else {
                    RespuestaJson::exito(["aulas" => $this->dao->listarAulas()]);
                }
                break;

            case "POST":
                $tipo = trim($datos["tipo"] ?? "");
                $numero = trim($datos["numero"] ?? "");

                if (empty($tipo) || empty($numero)) {
                    RespuestaJson::error("Faltan datos requeridos del aula.", 400);
                }
                if ($this->dao->existeAula($tipo, $numero)) {
                    RespuestaJson::error("El $tipo número $numero ya existe.", 409);
                }
                
                $this->dao->crearAula($tipo, $numero) 
                    ? RespuestaJson::exito(["mensaje" => "Aula agregada correctamente."], 201)
                    : RespuestaJson::error("Error al crear el aula.", 500);
                break;

            case "PUT":
                $id = (int)($datos["aulaId"] ?? 0);
                $tipo = trim($datos["tipo"] ?? "");
                $numero = trim($datos["numero"] ?? "");

                if ($id === 0 || empty($tipo) || empty($numero)) {
                    RespuestaJson::error("Faltan datos requeridos para modificar el aula.", 400);
                }

                $this->dao->modificarAula($id, $tipo, $numero)
                    ? RespuestaJson::exito(["mensaje" => "Aula modificada exitosamente."])
                    : RespuestaJson::error("No se pudo actualizar el aula.", 500);
                break;

            case "DELETE":
                $id = (int)($_GET["aulaId"] ?? $datos["aulaId"] ?? 0);
                
                if ($id <= 0) {
                    RespuestaJson::error("Identificador de aula no válido.", 400);
                }
                
                $this->dao->eliminarAula($id)
                    ? RespuestaJson::exito(["mensaje" => "Aula eliminada exitosamente."])
                    : RespuestaJson::error("No se pudo eliminar el aula (dependencias existentes).", 409);
                break;
                
            default:
                RespuestaJson::error("Método no permitido", 405);
        }
    }

    private function gestionarEquipo(string $metodo, array $datos): void {
        switch ($metodo) {
            case "POST":
                $aulaId = (int)($datos["aulaId"] ?? 0);
                $numPc = trim($datos["numPc"] ?? "");
                $modeloPc = trim($datos["modeloPc"] ?? "");
                $monitor = trim($datos["monitor"] ?? "");
                $modeloMouse = trim($datos["modeloMouse"] ?? "");
                $modeloTeclado = trim($datos["modeloTeclado"] ?? "");

                if ($aulaId === 0 || empty($numPc) || empty($modeloPc) || empty($monitor) || empty($modeloMouse) || empty($modeloTeclado)) {
                    RespuestaJson::error("Faltan datos requeridos del equipo.", 400);
                }

                $this->dao->agregarEquipo($aulaId, $numPc, $modeloPc, $monitor, $modeloMouse, $modeloTeclado)
                    ? RespuestaJson::exito(["mensaje" => "Equipo agregado correctamente."], 201)
                    : RespuestaJson::error("No se pudieron agregar los equipos.", 500);
                break;

            case "PUT":
                $numPc = trim($datos["numPc"] ?? "");
                $aulaId = (int)($datos["aulaId"] ?? 0);
                $modeloPc = trim($datos["modeloPc"] ?? "");
                $monitor = trim($datos["monitor"] ?? "");
                $modeloMouse = trim($datos["modeloMouse"] ?? "");
                $modeloTeclado = trim($datos["modeloTeclado"] ?? "");

                if (empty($numPc) || $aulaId === 0 || empty($modeloPc) || empty($monitor) || empty($modeloMouse) || empty($modeloTeclado)) {
                    RespuestaJson::error("Faltan datos del equipo.", 400);
                }

                $this->dao->modificarEquipo($numPc, $aulaId, $modeloPc, $monitor, $modeloMouse, $modeloTeclado)
                    ? RespuestaJson::exito(["mensaje" => "Equipo modificado correctamente."])
                    : RespuestaJson::error("No se pudo modificar el equipo.", 500);
                break;

            case "DELETE":
                $aulaId = (int)($_GET["aulaId"] ?? $datos["aulaId"] ?? 0);
                $numPc = trim($_GET["numPc"] ?? $datos["numPc"] ?? "");

                if (empty($numPc) || $aulaId === 0) {
                    RespuestaJson::error("Datos de equipo no válidos.", 400);
                }

                $this->dao->eliminarEquipo($numPc, $aulaId)
                    ? RespuestaJson::exito(["mensaje" => "Equipo eliminado correctamente."])
                    : RespuestaJson::error("No se pudo eliminar el equipo (puede tener tickets asociados).", 409);
                break;

            default:
                RespuestaJson::error("Método no permitido", 405);
        }
    }

    private function gestionarPortatil(string $metodo, array $datos): void {
        switch ($metodo) {
            case "GET":
                RespuestaJson::exito(["portatiles" => $this->dao->listarPortatiles()]);
                break;

            case "POST":
                $modelo = trim($datos["modeloPortatil"] ?? "");
                if (empty($modelo)) {
                    RespuestaJson::error("Falta el modelo del portátil.", 400);
                }

                $this->dao->crearPortatil($modelo)
                    ? RespuestaJson::exito(["mensaje" => "Portátil agregado correctamente."], 201)
                    : RespuestaJson::error("Error al guardar el portátil.", 500);
                break;

            case "PUT":
                $id = (int)($datos["portatilId"] ?? 0);
                $modelo = trim($datos["modeloPortatil"] ?? "");

                if ($id === 0 || empty($modelo)) {
                    RespuestaJson::error("Datos incompletos para modificar portátil.", 400);
                }

                $this->dao->modificarPortatil($id, $modelo)
                    ? RespuestaJson::exito(["mensaje" => "Portátil modificado correctamente."])
                    : RespuestaJson::error("No se pudo modificar el portátil.", 500);
                break;

            case "PATCH":
                $id = (int)($datos["portatilId"] ?? 0);
                $accion = trim($datos["accion"] ?? ""); 

                if ($id === 0) {
                    RespuestaJson::error("Portátil no válido.", 400);
                }

                if ($accion === "habilitar") {
                    $this->dao->habilitarPortatil($id)
                        ? RespuestaJson::exito(["mensaje" => "Portátil habilitado correctamente."])
                        : RespuestaJson::error("No se pudo habilitar (no está disponible).", 409);
                } else if ($accion === "deshabilitar") {
                    $this->dao->deshabilitarPortatil($id)
                        ? RespuestaJson::exito(["mensaje" => "Portátil deshabilitado correctamente."])
                        : RespuestaJson::error("No se pudo deshabilitar (está prestado o ya inactivo).", 409);
                } else {
                    RespuestaJson::error("Acción no válida.", 400);
                }
                break;

            case "DELETE":
                $id = (int)($_GET["portatilId"] ?? $datos["portatilId"] ?? 0);
                
                if ($id === 0) {
                    RespuestaJson::error("Portátil no válido.", 400);
                }

                $this->dao->eliminarPortatil($id)
                    ? RespuestaJson::exito(["mensaje" => "Portátil eliminado correctamente."])
                    : RespuestaJson::error("No se pudo eliminar (está en préstamo o tiene historial).", 409);
                break;

            default:
                RespuestaJson::error("Método no permitido", 405);
        }
    }
    
    private function gestionarPlanillas(string $metodo): void {
        if ($metodo !== "GET") {
            RespuestaJson::error("Método no permitido", 405);
        }
        
        $aulas = $this->dao->listarAulas();
        $aulasPorTipo = ["laboratorio" => [], "taller" => []];
        
        foreach ($aulas as $aula) {
            $tipo = strtolower($aula["tipo"] ?? ""); 
            if (isset($aulasPorTipo[$tipo])) {
                 $aulasPorTipo[$tipo][] = $aula["numero"];
            }
        }
        
        RespuestaJson::exito(["planillas" => $aulasPorTipo]);
    }
}
?>