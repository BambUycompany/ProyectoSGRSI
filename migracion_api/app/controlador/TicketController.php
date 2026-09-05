<?php 
require_once RUTA_MODELO . "/ConectorPDO";
require_once RUTA_MODELO . "/TicketDAO.php";
require_once RUTA_VISTA . "/RespuestajsonTicket.php";

class TicketController{
    public function gestionar(string $metodo): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }
        if (!($_SESSION["administrador"] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }
        match ($metodo) {
            "GET" => $this->listarAgrupados(),
            "POST" => $this->registrarTicket(),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }

    public function listarAgrupados(): void{
        $conexion = $this->conectar();
        $dao = new TicketDAO($conexion);
        RespuestaJson::exito($dao->listarTicketsAgrupados());
    }

    public function listarPorPCAULA():void{
        $conexion = $this->conectar();
        $dao = new TicketDAO($conexion);
        RespuestaJson::exito($dao->listarTicketsPorPcYAula());
    }

    public function registrarTicket():void{
        $this->verificarCsrf();
        $conexion = $this->conectar();
        $dao = new TicketDAO($conexion);
        RespuestaJson::exito($dao->registrarTicket());

    }

     private function verificarCsrf(): void
    {
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