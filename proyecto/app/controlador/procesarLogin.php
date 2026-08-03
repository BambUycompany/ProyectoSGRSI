<?php
if(isset($_GET["error"])){
    echo "<p style='color:red;'>Cédula o contraseña incorrecta</p>";
}
require_once __DIR__ . "/../modelo/Usuario.php";
require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDatosUsuario.php";
require_once __DIR__ . "/../modelo/Login.php";

//Comprueba que el formulario haya sido enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: petición inválida.";

    header("Location: login.php?error=" . urlencode($mensaje));
    exit;
}

//Recupera las credenciales provenientes del formulario
$cedula = trim($_POST["cedula"] ?? "");
$password = $_POST["password"] ?? "";

$conectorPDO = new ConectorPDO("localhost:3306", "root", "", "SGRSI_db");
$conexion = $conectorPDO->establecerConexion();

    $accesoDatosUsuario = new AccesoDatosUsuario($conexion);
    $login = new Login($accesoDatosUsuario);
    $usuario = $login->autenticar($cedula, $password);

$conectorPDO->desconectar();


//Si las credenciales no coinciden, muestra el error y detiene el proceso
if ($usuario === null) {
    header("Location: login.php?error=" . urlencode("La cédula o la contraseña son incorrectas."));
    exit;
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

if($_SESSION["administrador"]&&$_SESSION["soporte"]&&$_SESSION["solicitante"]){ 
    header("Location: ../vista/administrador.php");
    exit;
} elseif($_SESSION["soporte"]) {
    header("Location: ../vista/soporte.php");
    exit;
} elseif($_SESSION["solicitante"]) {
    header("Location: ../vista/solicitante.php");
    exit;

}

exit;

?>