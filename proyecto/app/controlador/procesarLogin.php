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

//restricciones de acceso

if($usuario === null){
    header("Location: login.php?error=credenciales");
    exit;
}

if(!$usuario ->estaActivo()){
    header("Location: login.php?error=usuarioInactivo");
    exit;
}

$roles = [];
if ($usuario->esAdministrador()) $roles[] = "administrador";
if ($usuario->esSoporte())       $roles[] = "soporte";
if ($usuario->esSolicitante())   $roles[] = "solicitante";


if (count($roles) === 0) {
    header("Location: ../vista/login.php?error=sinRol");
    exit;
}


session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["soporte"] = $usuario->esSoporte();
$_SESSION["solicitante"] = $usuario->esSolicitante();

if(count($roles) > 1){
    header("Location: ../vista/seleccionDashboard.php");
    exit;

}

if($_SESSION["administrador"]){ 
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