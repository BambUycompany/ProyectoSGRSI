<?php
require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_MODELO. "/UsuarioDAO.php";


class LoginController {

    public function gestionar(string $metodo): void {
        match ($metodo) {
            "POST" => $this->autenticar($metodo),
            "DELETE" => $this->cerrarSesion($metodo),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }

    public function autenticar(string $metodo): void {
        if ($metodo !== "POST") {
            RespuestaJson::error("Método no permitido", 405);
        }

        $datos = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        $cedula = trim($datos["cedula"] ?? "");
        $clave = $datos["clave"] ?? "";

        if (empty($cedula) || empty($clave)) {
            RespuestaJson::error("Credenciales incompletas", 400);
        }

        $conexion = $this->conectar();
        $dao = new UsuarioDAO($conexion);

        $usuario = $dao->buscarUsuario($cedula);

        if ($usuario === null || !password_verify($clave, $usuario["claveHash"])) {
            RespuestaJson::error("Cédula o contraseña incorrecta", 401);
        }

        if (!$usuario["activo"]) {
            RespuestaJson::error("Usuario inactivo", 403);
        }

        $roles = [];
        if ($usuario["administrador"]) $roles[] = "administrador";
        if ($usuario["soporte"])       $roles[] = "soporte";
        if ($usuario["solicitante"])   $roles[] = "solicitante";

        if (empty($roles)) {
            RespuestaJson::error("El usuario no tiene ningún rol asignado", 403);
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);

        if (empty($_SESSION["csrfToken"])) {
            $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
        }

        $_SESSION["cedula"] = $usuario["cedula"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["apellido"] = $usuario["apellido"];
        $_SESSION["administrador"] = $usuario["administrador"];
        $_SESSION["soporte"] = $usuario["soporte"];
        $_SESSION["solicitante"] = $usuario["solicitante"];
        $_SESSION["roles"] = $roles;

        unset($_SESSION["rolActivo"]);

        $rolActivo = count($roles) === 1 ? $roles[0] : null;

        if ($rolActivo !== null) {
            $_SESSION["rolActivo"] = $rolActivo;
        }



        RespuestaJson::exito([
            "mensaje" => "Autenticación exitosa",
            "cedula" => $usuario["cedula"],
            "nombre" => $usuario["nombre"],
            "apellido" => $usuario["apellido"],
            "roles" => $roles,
            "rolActivo" => $rolActivo,
            "csrfToken" => $_SESSION["csrfToken"]
        ]);
    }

    public function cerrarSesion(string $metodo): void {
        if ($metodo !== "DELETE" && $metodo !== "POST") {
            RespuestaJson::error("Método no permitido", 405);
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();

        RespuestaJson::exito(["mensaje" => "Sesión cerrada correctamente"]);
    }

    private function conectar(): PDO {
        $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
        $conexion = $conector->establecerConexion();
        if ($conexion === null) {
            RespuestaJson::error("Error de conexión a la base de datos", 500);
        }
        return $conexion;
    }
}
?>