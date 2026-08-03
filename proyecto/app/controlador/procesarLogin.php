<?php
if(isset($_GET["error"])){
    echo "<p style='color:red;'>Cédula o contraseña incorrecta</p>";
}
require_once __DIR__ . "/../modelo/Usuario.php";
require_once __DIR__ . "/../modelo/consultaUsuario.php";
require_once __DIR__ . "/../modelo/Login.php";

//Comprueba que el formulario haya sido enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

//Recupera las credenciales provenientes del formulario
$cedula = trim($_POST["cedula"] ?? "");
$password = $_POST["password"] ?? "";

$consultaUsuario = new ConsultaUsuario();
$login = new Login($consultaUsuario);

$usuario = $login->autenticar($cedula, $password);

//Si las credenciales no coinciden, muestra el error y detiene el proceso
if ($usuario === null) {
    exit("La cédula o la contraseña son incorrectas.");
}

//Solo se encuentra implementado el rol administrador
//if (!$usuario->esAdministrador()) {
    //exit("El usuario no tiene acceso al panel de administración.");
//}

session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["soporte"] = $usuario->esSoporte();
$_SESSION["solicitante"] = $usuario->esSolicitante();

header("Location: ../vista/administrador.php"); 
header("Location: ../vista/soporte.php"); 
header("Location: ../vista/solicitante.php"); 
exit;

?>