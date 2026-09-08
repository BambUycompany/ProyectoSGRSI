<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/UsuarioDAO.php";
require_once RUTA_VISTA . "/RespuestaJsonUsuario.php";

class LoginController {

    public function gestionar(string $metodo): void {
        match ($metodo) {
            "POST" => $this->autenticar(),
            "DELETE" => $this->cerrarSesion(),
            default => RespuestaJsonUsuario::error("Método no permitido", 405)
        };
    }

    public function autenticar(): void {
        $datos = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        $cedula = trim($datos["cedula"] ?? "");
        $password = $datos["password"] ?? "";

        if (empty($cedula) || empty($password)) {
            RespuestaJsonUsuario::error("Credenciales incompletas", 400);
        }

        $conexion = $this->conectar();
        $dao = new UsuarioDAO($conexion);

        $usuario = $dao->buscarUsuario($cedula);

        if ($usuario === null || !password_verify($password, $usuario["claveHash"])) {
            RespuestaJsonUsuario::error("Cédula o contraseña incorrecta", 401);
        }

        if (!$usuario["activo"]) {
            RespuestaJsonUsuario::error("Usuario inactivo", 403);
        }

        $roles = [];
        if ($usuario["administrador"]) $roles[] = "administrador";
        if ($usuario["soporte"])       $roles[] = "soporte";
        if ($usuario["solicitante"])   $roles[] = "solicitante";

        if (empty($roles)) {
            RespuestaJsonUsuario::error("El usuario no tiene ningún rol asignado", 403);
        }

        session_regenerate_id(true);

        if (empty($_SESSION["csrfToken"])) {
            $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
        }

        $_SESSION["cedula"] = $usuario["cedula"];
        $_SESSION["administrador"] = $usuario["administrador"];
        $_SESSION["soporte"] = $usuario["soporte"];
        $_SESSION["solicitante"] = $usuario["solicitante"];
        $_SESSION["roles"] = $roles;

        $rolActivo = count($roles) === 1 ? $roles[0] : null;
        if ($rolActivo) {
            $_SESSION["rolActivo"] = $rolActivo;
        }

        RespuestaJsonUsuario::exito([
            "mensaje" => "Autenticación exitosa",
            "cedula" => $usuario["cedula"],
            "roles" => $roles,
            "rolActivo" => $rolActivo,
            "csrfToken" => $_SESSION["csrfToken"]
        ]);
    }

    public function cerrarSesion(): void {
        $_SESSION = [];
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../public/login.php");
        exit;
        require_once __DIR__ . "/../config/config.php";
        RespuestaJsonUsuario::exito(["mensaje" => "Sesión cerrada correctamente"]);
    }

    private function conectar(): PDO {
        $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
        $conexion = $conector->establecerConexion();
        if ($conexion === null) {
            RespuestaJsonUsuario::error("Error de conexión a la base de datos", 500);
        }
        return $conexion;
    }
}