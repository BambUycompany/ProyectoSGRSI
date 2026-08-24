<?php 
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AltaDatosUsuarios.php";

session_start();

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: RUTA_PUBLIC . "/altaUsuario.php?error=metodoNoPermitido");
    exit;
}


$cedula = trim($_POST["cedula"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$clave = trim($_POST["claveHash"] ?? "");
$confirmarClave = trim($_POST["confirmarClave"] ?? "");
$rol = trim($_POST["rol"] ?? "");

//restricciones
if ($cedula === "" || $nombre === "" || $apellido === "" || $clave === "" || $confirmarClave === "" || $rol === "" ) {
    $mensaje = "No se pudo registrar el empleado: existen campos vacíos." . $rol;
    header("Location: ../../public/registro_empleados.php?error=" . urlencode($mensaje));
    exit;
}

if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
    $mensaje = "No se pudo registrar el empleado: cédula incorrecta.";

    header("Location: ../../public/registro_empleados.php?error=" . urlencode($mensaje));
    exit;
}

if (strlen($clave) < 12) {
    $mensaje = "La contraseña debe contener al menos 12 caracteres.";

    header("Location: ../../public/registro_empleados.php?error=" . urlencode($mensaje));
    exit;
}

if ($clave !== $confirmarClave) {
    $mensaje = "Las contraseñas ingresadas no coinciden.";

    header("Location: ../../public/registro_empleados.php?error=" . urlencode($mensaje));
    exit;
}

$claveHash = password_hash($clave, PASSWORD_DEFAULT);

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$altaDatosUsuarios = new AltaDatosUsuarios($conexion);

$resultado = $altaDatosUsuarios->registrarUsuario($cedula, $nombre, $apellido, $claveHash, $rol);
$conectorPDO = null;

if (!$resultado) {
    $mensaje = "No se pudo registrar el empleado";
    header("Location: ../../public/registro_empleados.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Usuario ingresado exitosamente.";
header("Location: ../../public/registro_empleados.php?resultado=" . urlencode($mensaje));
exit;



?>