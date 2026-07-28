<?php
if(isset($_GET["error"])){
    echo "<p style='color:red;'>Cédula o contraseña incorrecta</p>";
}
require_once __DIR__ . "/../modelo/Usuario.php";
require_once __DIR__ . "/../modelo/consultaUsuario.php";
require_once __DIR__ . "/../modelo/Login.php";

//Comprueba que el formulario haya sido enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../vista/login.php");
    exit;
}

//Recupera las credenciales provenientes del formulario
$cedula = trim($_POST["cedula"] ?? "");
$password = $_POST["password"] ?? "";

$consultaUsuario = new ConsultaUsuario();
$login = new Login($consultaUsuario);

$usuario = $login->autenticar($cedula, $password);

if ($usuario === null) {
    exit("La cédula o la contraseña son incorrectas.");
}

if (method_exists($usuario, 'estaActivo') && !$usuario->estaActivo()) {
    header("Location: ../vista/Login.php?error=inactivo");
    exit;
}

session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["soporte"] = $usuario->esSoporte();
$_SESSION["solicitante"] = $usuario->esSolicitante();

if ($usuario->esAdministrador()) {
    header("Location: ../vista/administrador.php");
    exit;
} elseif ($usuario->esSoporte()) {
    header("Location: ../vista/soporte.php");
    exit;
} elseif ($usuario->esSolicitante()) {
    header("Location: ../vista/solicitante.php");
    exit;
} else {
    // Si no tiene un rol asignado válido
    header("Location: ../vista/Login.php?error=sin_rol");
    exit;
}
?>