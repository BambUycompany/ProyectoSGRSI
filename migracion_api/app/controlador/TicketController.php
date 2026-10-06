<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/TicketDAO.php";
require_once RUTA_MODELO . "/ModificarDatosTickets.php";
require_once RUTA_VISTA . "/Respuestajson.php";

class TicketController
{
    public function gestionar(): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        if (!($_SESSION["soporte"] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $accion = $_GET['accion'] ?? '';

        if ($metodo === 'GET') {
            match ($accion) {
                'listarPorPCAULA' => $this->listarPorPCAULA(),
                default => RespuestaJson::error("Acción GET no permitida", 405),
            };
        } elseif ($metodo === 'POST' || $metodo === 'PUT') {
            match ($accion) {
                'registrar' => $this->registrarTicket(),
                'cambiarEstado' => $this->cambiarEstado(),
                'cambiarPrioridad' => $this->cambiarPrioridad(),
                'finalizar' => $this->finalizar(),
                default => RespuestaJson::error("Acción no permitida", 405),
            };
        } else {
            RespuestaJson::error("Método no permitido", 405);
        }
    }

    private function registrarTicket(): void
    {
        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        if (empty($datos) || !isset($datos['numeroPc'], $datos['fallo'], $datos['aulaId'])) {
            RespuestaJson::error("Faltan datos obligatorios para registrar el ticket.", 400);
            return;
        }

        // Cédula del registrante obtenida desde la sesión si no viene especificada
        $datos['documentoRegistrante'] = $datos['documentoRegistrante'] ?? $_SESSION['cedula'] ?? '';

        $conexion = $this->conectar();
        $dao = new TicketDAO($conexion);

        $resultado = $dao->registrarTicket($datos);

        if ($resultado) {
            echo json_encode(["estado" => "exito", "mensaje" => "Ticket registrado correctamente."]);
        } else {
            RespuestaJson::error("Error al registrar el ticket en la base de datos", 500);
        }
    }

    private function cambiarEstado(): void
    {
        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $id = (int)($datos['id'] ?? 0);
        $nuevoEstado = trim($datos['nuevoEstado'] ?? '');

        if ($id <= 0 || $nuevoEstado === '') {
            RespuestaJson::error("Faltan datos obligatorios (id, nuevoEstado)", 400);
            return;
        }

        $modificador = new ModificarDatosTickets($this->conectar());
        $exito = $modificador->cambiarEstadoTicket($id, $nuevoEstado)[cite: 2];

        if ($exito) {
            echo json_encode(["estado" => "exito", "mensaje" => "Estado del ticket actualizado."]);
        } else {
            RespuestaJson::error("Error al cambiar el estado o estado no permitido.", 400);
        }
    }

    private function cambiarPrioridad(): void
    {
        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $id = (int)($datos['id'] ?? 0);
        $nuevaPrioridad = trim($datos['nuevaPrioridad'] ?? '');

        if ($id <= 0 || $nuevaPrioridad === '') {
            RespuestaJson::error("Faltan datos obligatorios (id, nuevaPrioridad)", 400);
            return;
        }

        $modificador = new ModificarDatosTickets($this->conectar());
        $exito = $modificador->cambiarPrioridadTicket($id, $nuevaPrioridad)[cite: 2];

        if ($exito) {
            echo json_encode(["estado" => "exito", "mensaje" => "Prioridad del ticket actualizada."]);
        } else {
            RespuestaJson::error("Error al cambiar la prioridad o prioridad no permitida.", 400);
        }
    }

    private function finalizar(): void
    {
        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $id = (int)($datos['id'] ?? 0);
        $diagnostico = trim($datos['diagnostico'] ?? '');
        $soporteCedula = $_SESSION["cedula"] ?? trim($datos['soporteCedula'] ?? '');

        if ($id <= 0 || $diagnostico === '' || $soporteCedula === '') {
            RespuestaJson::error("Faltan datos obligatorios (id, diagnostico, soporteCedula)", 400);
            return;
        }

        $modificador = new ModificarDatosTickets($this->conectar());
        $exito = $modificador->finalizarTicket($id, $diagnostico, $soporteCedula)[cite: 2];

        if ($exito) {
            echo json_encode(["estado" => "exito", "mensaje" => "Ticket finalizado correctamente."]);
        } else {
            RespuestaJson::error("Error al finalizar el ticket.", 500);
        }
    }

    public function listarPorPCAULA(): void
    {
        $conexion = $this->conectar();
        $dao = new TicketDAO($conexion);
        $aulaId = $_GET['aulaId'] ?? null;
        $pc = $_GET['pc'] ?? null;

        echo json_encode($dao->listarPorPCAULA($aulaId, $pc));
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




    

}