<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/UsuarioDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class UsuarioController {
    private UsuarioDAO $dao;

    public function __construct() {
        $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
        $conexion = $conector->establecerConexion();
        if ($conexion === null) {
            RespuestaJson::error("Error de conexión a la base de datos", 500);
        }
        $this->dao = new UsuarioDAO($conexion);
    }

    public function gestionar(string $metodo): void {
        if (empty($_SESSION["cedula"])) {
            RespuestaJson::error("No autorizado. Sesión no iniciada.", 401);
        }
        if (empty($_SESSION["administrador"])) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        $datos = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        match ($metodo) {
            "GET" => $this->listarOObtener(),
            "POST" => $this->altaUsuario($datos),
            "PUT" => $this->modificarUsuario($datos),
            "DELETE" => $this->eliminarUsuario($datos),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }

    private function listarOObtener(): void {
        $cedula = trim($_GET["cedula"] ?? "");

        if ($cedula !== "") {
            $usuario = $this->dao->obtenerPorCedula($cedula);
            $usuario === null
                ? RespuestaJson::error("Usuario no encontrado.", 404)
                : RespuestaJson::exito($usuario);
            return;
        }

        RespuestaJson::exito($this->dao->listarUsuarios());
    }

    private function altaUsuario(array $datos): void {
        $cedula = trim($datos["cedula"] ?? "");
        $nombre = trim($datos["nombre"] ?? "");
        $apellido = trim($datos["apellido"] ?? "");
        $clave = trim($datos["clave"] ?? "");
        $confirmarClave = trim($datos["confirmarClave"] ?? "");
        $rol = trim($datos["rol"] ?? "");

        if ($cedula === "" || $nombre === "" || $apellido === "" || $clave === "" || $rol === "") {
            RespuestaJson::error("Faltan campos obligatorios.", 400);
        }
        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("Cédula incorrecta.", 400);
        }
        if (strlen($clave) < 12) {
            RespuestaJson::error("La contraseña debe tener al menos 12 caracteres.", 400);
        }
        if ($clave !== $confirmarClave) {
            RespuestaJson::error("Las contraseñas no coinciden.", 400);
        }

        $claveHash = password_hash($clave, PASSWORD_DEFAULT);

        $this->dao->crearUsuario($cedula, $nombre, $apellido, $claveHash, $rol)
            ? RespuestaJson::exito(["mensaje" => "Usuario agregado correctamente."], 201)
            : RespuestaJson::error("No se pudo crear el usuario.", 500);
    }

    private function modificarUsuario(array $datos): void {
        $cedula = trim($datos["cedula"] ?? "");
        $nombre = trim($datos["nombre"] ?? "");
        $apellido = trim($datos["apellido"] ?? "");
        $clave = trim($datos["clave"] ?? "");
        $rol = trim($datos["rol"] ?? "");

        if ($cedula === "" || $nombre === "" || $apellido === "" || $rol === "") {
            RespuestaJson::error("Faltan campos obligatorios.", 400);
        }

        $claveHash = $clave !== "" ? password_hash($clave, PASSWORD_DEFAULT) : null;

        $this->dao->modificarUsuario($cedula, $nombre, $apellido, $claveHash, $rol)
            ? RespuestaJson::exito(["mensaje" => "Usuario modificado correctamente."])
            : RespuestaJson::error("No se pudo modificar el usuario.", 500);
    }

    private function eliminarUsuario(array $datos): void {
        $cedula = trim($datos["cedula"] ?? "");

        if ($cedula === "") {
            RespuestaJson::error("Cédula no válida.", 400);
        }

        $this->dao->desactivarUsuario($cedula)
            ? RespuestaJson::exito(["mensaje" => "Usuario desactivado correctamente."])
            : RespuestaJson::error("No se pudo desactivar el usuario.", 500);
    }
}
?>