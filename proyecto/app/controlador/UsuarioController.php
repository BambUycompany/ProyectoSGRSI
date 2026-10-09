
<?php

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/UsuarioDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class UsuarioController {

    private UsuarioDAO $dao;

    public function __construct() {

        $conector = new ConectorPDO(
            $_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"],
            $_ENV["DB_USUARIO"],
            $_ENV["DB_CLAVE"],
            $_ENV["DB_NOMBRE"]
        );

        $conexion = $conector->establecerConexion();

        if ($conexion === null) {
            RespuestaJson::error(
                "Error de conexión a la base de datos.",
                500
            );
        }

        $this->dao = new UsuarioDAO($conexion);
    }

    public function gestionar(string $metodo): void {

        if (empty($_SESSION["cedula"])) {
            RespuestaJson::error(
                "No autorizado. Debe iniciar sesión.",
                401
            );
        }

        if (empty($_SESSION["administrador"])) {
            RespuestaJson::error(
                "Acceso denegado. Se requiere el rol administrador.",
                403
            );
        }

        if ($metodo !== "GET") {

            $token = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
            $tokenSesion = $_SESSION["csrfToken"] ?? "";

            if (
                !is_string($token) ||
                !is_string($tokenSesion) ||
                $tokenSesion === "" ||
                !hash_equals($tokenSesion, $token)
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

            $datos = json_decode($contenido ?: "{}", true);

            if (
                !is_array($datos) ||
                array_is_list($datos)
            ) {
                RespuestaJson::error(
                    "Los datos enviados no son válidos.",
                    400
                );
            }
        }

        switch ($metodo) {

            case "GET":
                $this->listarOObtener();
                break;

            case "POST":
                $this->altaUsuario($datos);
                break;

            case "PUT":
                $this->modificarUsuario($datos);
                break;

            case "DELETE":
                $this->desactivarUsuario($datos);
                break;
            
            case "PATCH":
                $this->reactivarUsuario($datos);
                break;

            default:
                RespuestaJson::error(
                    "Método no permitido.",
                    405
                );
        }
    }

    private function listarOObtener(): void {

        $cedula = trim($_GET["cedula"] ?? "");

        if ($cedula !== "") {

            $usuario = $this->dao->obtenerPorCedula($cedula);

            if ($usuario === null) {
                RespuestaJson::error(
                    "Empleado no encontrado.",
                    404
                );
            }

            RespuestaJson::exito($usuario);
        }

        RespuestaJson::exito(
            $this->dao->listarUsuarios()
        );
    }

    private function validarRoles($roles): array {

        $rolesPermitidos = [
            "administrador",
            "soporte",
            "solicitante"
        ];

        if (!is_array($roles) || count($roles) === 0) {
            RespuestaJson::error(
                "Debe seleccionar al menos un rol.",
                400
            );
        }

        $roles = array_values(array_unique($roles));

        foreach ($roles as $rol) {

            if (
                !is_string($rol) ||
                !in_array($rol, $rolesPermitidos, true)
            ) {
                RespuestaJson::error(
                    "Se seleccionó un rol inválido.",
                    400
                );
            }
        }

        return $roles;
    }

    private function altaUsuario(array $datos): void {

        $cedula = trim($datos["cedula"] ?? "");
        $nombre = trim($datos["nombre"] ?? "");
        $apellido = trim($datos["apellido"] ?? "");
        $clave = $datos["clave"] ?? "";
        $confirmarClave = $datos["confirmarClave"] ?? "";

        $roles = $this->validarRoles(
            $datos["roles"] ?? []
        );

        if (
            $cedula === "" ||
            $nombre === "" ||
            $apellido === "" ||
            $clave === ""
        ) {
            RespuestaJson::error(
                "Complete todos los campos obligatorios.",
                400
            );
        }

        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error(
                "La cédula debe contener ocho dígitos.",
                400
            );
        }

        if (
            strlen($nombre) > 50 ||
            strlen($apellido) > 50
        ) {
            RespuestaJson::error(
                "El nombre o apellido es demasiado largo.",
                400
            );
        }

        if (
            !is_string($clave) ||
            strlen($clave) < 8
        ) {
            RespuestaJson::error(
                "La contraseña debe contener al menos 8 caracteres.",
                400
            );
        }

        if ($clave !== $confirmarClave) {
            RespuestaJson::error(
                "Las contraseñas no coinciden.",
                400
            );
        }

        if ($this->dao->obtenerPorCedula($cedula) !== null) {
            RespuestaJson::error(
                "Ya existe un empleado con esa cédula.",
                409
            );
        }

        $claveHash = password_hash(
            $clave,
            PASSWORD_DEFAULT
        );

        if (!$this->dao->crearUsuario(
            $cedula,
            $nombre,
            $apellido,
            $claveHash,
            $roles
        )) {
            RespuestaJson::error(
                "No se pudo registrar el empleado.",
                500
            );
        }

        RespuestaJson::exito([
            "mensaje" => "Empleado registrado correctamente."
        ], 201);
    }

    private function modificarUsuario(array $datos): void {

        $cedula = trim($datos["cedula"] ?? "");
        $nombre = trim($datos["nombre"] ?? "");
        $apellido = trim($datos["apellido"] ?? "");

        $clave = $datos["clave"] ?? "";
        $confirmarClave = $datos["confirmarClave"] ?? "";

        $roles = $this->validarRoles(
            $datos["roles"] ?? []
        );

        if (
            $cedula === "" ||
            $nombre === "" ||
            $apellido === ""
        ) {
            RespuestaJson::error(
                "Complete los campos obligatorios.",
                400
            );
        }

        if (
            !preg_match("/^[1-9][0-9]{7}$/", $cedula) ||
            strlen($nombre) > 50 ||
            strlen($apellido) > 50
        ) {
            RespuestaJson::error(
                "Los datos del empleado no son válidos.",
                400
            );
        }

        $usuarioActual = $this->dao->obtenerPorCedula($cedula);

        if ($usuarioActual === null) {
            RespuestaJson::error(
                "Empleado no encontrado.",
                404
            );
        }

        $claveHash = null;

        if ($clave !== "") {

            if (
                !is_string($clave) ||
                strlen($clave) < 12
            ) {
                RespuestaJson::error(
                    "La nueva contraseña debe tener al menos 12 caracteres.",
                    400
                );
            }

            if ($clave !== $confirmarClave) {
                RespuestaJson::error(
                    "Las contraseñas no coinciden.",
                    400
                );
            }

            $claveHash = password_hash(
                $clave,
                PASSWORD_DEFAULT
            );
        }

        if (!$this->dao->modificarUsuario(
            $cedula,
            $nombre,
            $apellido,
            $claveHash,
            $roles
        )) {
            RespuestaJson::error(
                "No se pudo modificar el empleado.",
                500
            );
        }

        RespuestaJson::exito([
            "mensaje" => "Empleado modificado correctamente."
        ]);
    }

    private function desactivarUsuario(array $datos): void {

        $cedula = trim($datos["cedula"] ?? "");

        if ($cedula === "") {
            RespuestaJson::error(
                "Debe indicar la cédula del empleado.",
                400
            );
        }

        if ($cedula === $_SESSION["cedula"]) {
            RespuestaJson::error(
                "No puede desactivar su propia cuenta.",
                403
            );
        }

        $usuario = $this->dao->obtenerPorCedula($cedula);

        if ($usuario === null) {
            RespuestaJson::error(
                "Empleado no encontrado.",
                404
            );
        }

        if (!$this->dao->desactivarUsuario($cedula)) {
            RespuestaJson::error(
                "No se pudo desactivar el empleado.",
                409
            );
        }

        RespuestaJson::exito([
            "mensaje" => "Empleado desactivado correctamente."
        ]);
    }

   private function reactivarUsuario(array $datos): void {

        $cedula = trim($datos["cedula"] ?? "");

        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error(
                "La cédula indicada no es válida.",
                400
            );
        }

        $usuario = $this->dao->obtenerPorCedula($cedula);

        if ($usuario === null) {
            RespuestaJson::error(
                "Empleado no encontrado.",
                404
            );
        }

        if ((int) $usuario["activo"] === 1) {
            RespuestaJson::error(
                "El empleado ya se encuentra activo.",
                409
            );
        }

        if (!$this->dao->reactivarUsuario($cedula)) {
            RespuestaJson::error(
                "No se pudo reactivar el empleado.",
                409
            );
        }

        RespuestaJson::exito([
            "mensaje" => "Empleado reactivado correctamente."
        ]);
    }
}
