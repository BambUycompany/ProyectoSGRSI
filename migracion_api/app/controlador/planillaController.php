<?php
require_once RUTA_MODELO . "/ConectorPDO";
require_once RUTA_MODELO . "/PlanillaDAO.php";
require_once RUTA_MODELO . "/TicketDAO.php";
require_once RUTA_VISTA . "/RespuestajsonPlanilla.php";

class planillaController
{
    public function gestionarPlanilla(): void
    {
       if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }
        if (!(($_SESSION["soporte"] ?? false || $_SESSION["solicitante"] ?? false))) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        match ($metodo) {
            "GET" => $this->listarRegistroPlanilla(),
            "POST" => $this->Registro(),
           // "PATCH" => $this->modificar(),
            //"DELETE" => $this->baja(),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }
    private function Registro(): void{  

        $this->verificarCsrf();

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

            
        $tipo = trim($datos["tipo"] ?? "");
        $numero = trim($datos["numero"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $horaEntrada = trim($_POST["horaEntrada"] ?? "");
        $horaSalida = trim($_POST["horaSalida"] ?? "");
        $nombreSolicitante = trim($datos["nombreSolicitante"] ?? "");
        $asignatura = trim($datos["Asignatura"] ?? "") ?: null;
        $grupo = trim($datos["grupo"] ?? "") ?: null;
        $turno = trim($datos["turno"] ?? "") ?: null;
        $tickets = $datos["tickets"] ?? []; 
        $documentoRegistrante = $datos["cedula"];

        if ($tipo === "" || $numero === "" || $fecha === "" || $horaEntrada === "" || $horaSalida === "" || $nombreSolicitante === "") {
            header("Location: ../../public/registro_planilla.php?error=" . urlencode("Faltan campos obligatorios."));
            exit;
        }

        $accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);

        $aulaId = $dao->buscarAulaId($tipo, $numero);

        if ($aulaId === null) {
            header("Location: ../../public/registro_planilla.php?error=" . urlencode("El aula seleccionada no existe."));
            exit;
        }
        $datosPlanilla = [
            "fecha" => $fecha,
            "horaEntrada" => $horaEntrada,
            "horaSalida" => $horaSalida,
            "grupo" => $grupo,
            "turno" => $turno,
            "asignatura" => $asignatura,
            "nombreSolicitante" => $nombreSolicitante,
            "documentoRegistrante" => $documentoRegistrante,
            "aulaId" => $aulaId,
        ];

        $planillaId = $dao->registrarPlanilla($datosPlanilla);

        foreach ($tickets as $ticket) {
            $datosTicket = [
                "numeroPc" => $ticket["numeroPc"] ?? "",
                "fallo" => $ticket["fallo"] ?? "",
                "descripcion" => $ticket["descripcion"] ?? "",
                "aulaId" => $aulaId,
                "documentoRegistrante" => $documentoRegistrante,
                "planillaId" => $planillaId,
            ];

            $accesoDatosPlanilla->registrarTicket($datosTicket);
        }

        header("Location: ../../public/registro_planilla.php?resultado=" . urlencode("Registro guardado correctamente."));
        exit;


    
    }

    public function listarRegistroPlanilla() {
         $sql = "SELECT 
                PLANILLA.ID,
                PLANILLA.Fecha,
                PLANILLA.HoraEntrada,
                PLANILLA.HoraSalida,
                PLANILLA.NombreSolicitante,
                AULA.Numero AS AulaNumero,
                CASE 
                    WHEN EXISTS (SELECT 1 FROM LABORATORIO WHERE LABORATORIO.AulaID = AULA.ID) THEN 'laboratorio'
                    ELSE 'taller'
                END AS AulaTipo
            FROM PLANILLA
            JOIN AULA ON AULA.ID = PLANILLA.AulaID
            ORDER BY PLANILLA.Fecha DESC, PLANILLA.HoraEntrada DESC";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    
        private function verificarCsrf():void {    
            $token = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
            if (!isset($_SESSION["csrfToken"]) || !hash_equals($_SESSION["csrfToken"], $token)) {
                RespuestaJson::error("Solicitud rechazada", 403);
            }
        }

        private function conectar(): PDO
        {
            $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
            $conexion = $conector->establecerConexion();
            if ($conexion === null) {
                RespuestaJson::error("Error de conexión con la base de datos", 500);
            }
            return $conexion;
        }
        
    
}
?>







