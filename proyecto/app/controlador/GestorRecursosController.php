<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/GestorRecursosDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class GestorRecursosController {
    private GestorRecursosDAO $dao;

    public function __construct() {
        $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
        $conexion = $conector->establecerConexion();
        
        if ($conexion === null) {
            RespuestaJson::error("Error de conexión a la base de datos", 500);
        }
        $this->dao = new GestorRecursosDAO($conexion);
    }

    
    
public function gestionar(string $metodo, string $recurso): void {

    if (empty($_SESSION["cedula"])) {
        RespuestaJson::error(
            "Debe iniciar sesión para acceder a los recursos.",
            401
        );
    }
    $esAdministrador = !empty($_SESSION["administrador"]);
    $esSoporte = !empty($_SESSION["soporte"]);
    $esSolicitante = !empty($_SESSION["solicitante"]);

    if (!$esAdministrador && !$esSoporte && !$esSolicitante) {
        RespuestaJson::error("Acceso denegado.", 403);
    }
    if ($metodo !== "GET" && !$esAdministrador) {
        RespuestaJson::error(
            "No tiene permisos para modificar recursos.",
            403
        );
    }
    if ($metodo !== "GET") {
        $tokenRecibido = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
        $tokenSesion = $_SESSION["csrfToken"] ?? "";

        if (
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
    $datos = [];
    if ($metodo !== "GET") {
        $contenido = file_get_contents("php://input");

        if ($contenido !== false && trim($contenido) !== "") {
            $datos = json_decode($contenido, true);

            if (!is_array($datos) || array_is_list($datos)) {
                RespuestaJson::error(
                    "El cuerpo de la petición debe ser un objeto JSON.",
                    400
                );
            }
        }
    }

    match ($recurso) {
        "aula" => $this->gestionarAula($metodo, $datos),
        "equipo" => $this->gestionarEquipo($metodo, $datos),
        "portatil" => $this->gestionarPortatil($metodo, $datos),
        "planilla" => $this->gestionarPlanillas($metodo),

        default => RespuestaJson::error(
            "Recurso no encontrado.",
            404
        )
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
                    }
                    $equipos = $this->dao->listarEquiposDeAula($id);
                    RespuestaJson::exito(["aula" => $aula, "equipos" => $equipos]);
                } else {
                    RespuestaJson::exito(["aulas" => $this->dao->listarAulas()]);
                }
                break;

           
case "POST":
    $tipo = strtolower(trim($datos["tipo"] ?? ""));
    $numero = trim($datos["numero"] ?? "");

    if (
        !in_array($tipo, ["laboratorio", "taller"], true) ||
        $numero === ""
    ) {
        RespuestaJson::error(
            "Tipo o número de aula no válido.",
            400
        );
    }

    if ($this->dao->existeAula($tipo, $numero)) {
        RespuestaJson::error(
            "El aula ya se encuentra registrada.",
            409
        );
    }

    if (!$this->dao->crearAula($tipo, $numero)) {
        RespuestaJson::error(
            "No se pudo registrar el aula.",
            500
        );
    }

    RespuestaJson::exito(
        ["mensaje" => "Aula registrada correctamente."],
        201
    );
    break;

case "PUT":
    $id = (int)($datos["aulaId"] ?? 0);
    $tipo = strtolower(trim($datos["tipo"] ?? ""));
    $numero = trim($datos["numero"] ?? "");

    if (
        $id <= 0 ||
        !in_array($tipo, ["laboratorio", "taller"], true) ||
        $numero === ""
    ) {
        RespuestaJson::error(
            "Datos del aula no válidos.",
            400
        );
    }

    if ($this->dao->obtenerAulaPorId($id) === null) {
        RespuestaJson::error("El aula no existe.", 404);
    }

    if ($this->dao->existeAula($tipo, $numero, $id)) {
        RespuestaJson::error(
            "Ya existe otra aula de ese tipo con ese número.",
            409
        );
    }

    if (!$this->dao->modificarAula($id, $tipo, $numero)) {
        RespuestaJson::error(
            "No se pudo modificar el aula.",
            500
        );
    }
    RespuestaJson::exito(
        ["mensaje" => "Aula modificada correctamente."]
    );
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
            $cantidad = (int)($datos["cantidad"] ?? 1);

            $modeloPc = trim($datos["modeloPc"] ?? "");
            $monitor = trim($datos["monitor"] ?? "");
            $modeloMouse = trim($datos["modeloMouse"] ?? "");
            $modeloTeclado = trim($datos["modeloTeclado"] ?? "");

            if (
                $aulaId <= 0 ||
                $cantidad < 1 ||
                $cantidad > 100 ||
                $modeloPc === "" ||
                $monitor === "" ||
                $modeloMouse === "" ||
                $modeloTeclado === ""
            ) {
                RespuestaJson::error(
                    "Los datos del equipo son incompletos o inválidos.",
                    400
                );
            }

            if ($this->dao->obtenerAulaPorId($aulaId) === null) {
                RespuestaJson::error("El aula no existe.", 404);
            }

            $resultado = $this->dao->agregarEquipos(
                $aulaId,
                $cantidad,
                $modeloPc,
                $monitor,
                $modeloMouse,
                $modeloTeclado
            );
            if (!$resultado) {
                RespuestaJson::error(
                    "No se pudieron registrar los equipos.",
                    500
                );
            }
            RespuestaJson::exito(
                ["mensaje" => "Equipos registrados correctamente."],
                201
            );
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
                RespuestaJson::exito(["portatiles" => $this->dao->listarTodos()]);
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
        RespuestaJson::error(
            "Método no permitido.",
            405
        );
    }

    $aulas = $this->dao->listarAulas();

    $aulasPorTipo = [
        "laboratorio" => [],
        "taller" => []
    ];

    foreach ($aulas as $aula) {

        $tipo = strtolower($aula["Tipo"] ?? "");
        $numero = $aula["Numero"] ?? "";

        if (isset($aulasPorTipo[$tipo])) {
            $aulasPorTipo[$tipo][] = $numero;
        }
    }

    RespuestaJson::exito([
        "planillas" => $aulasPorTipo
    ]);
}

}