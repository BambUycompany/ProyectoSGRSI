<?php

require_once __DIR__ . "/../modelo/Usuario.php";
require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDatosUsuario.php";
require_once __DIR__ . "/../modelo/Login.php";

//Comprueba que el formulario haya sido enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: petición inválida.";
    header("Location: ../../public/login.php?error=sinSesion");
    exit;
}

//Recupera las credenciales provenientes del formulario
$cedula = trim($_POST["cedula"] ?? "");
$password = $_POST["password"] ?? "";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

    $accesoDatosUsuario = new AccesoDatosUsuario($conexion);
    $login = new Login($accesoDatosUsuario);
    $usuario = $login->autenticar($cedula, $password);

$conectorPDO->desconectar();

//restricciones de acceso

if($usuario === null){
    header("Location: ../../public/login.php?error=credenciales");
    exit;
}

if(!$usuario ->estaActivo()){
    header("Location: ../../public/login.php?error=usuarioInactivo");
    exit;
}

$roles = [];
if ($usuario->esAdministrador()) $roles[] = "administrador";
if ($usuario->esSoporte())       $roles[] = "soporte";
if ($usuario->esSolicitante())   $roles[] = "solicitante";


if (count($roles) === 0) {
    header("Location: ../../public/login.php?error=sinRol");
    exit;
}


session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["soporte"] = $usuario->esSoporte();
$_SESSION["solicitante"] = $usuario->esSolicitante();
$_SESSION["roles"] = $roles; 

if (count($roles) === 1) {
    $_SESSION["rolActivo"] = $roles[0];
} 

if (count($roles) > 1) {
    header("Location: ../../public/seleccion_dashboard.php"); 
    exit;
}

if($_SESSION["rolActivo"] === "administrador") { 
    header("Location: ../../public/administrador.php");
    exit;
} elseif($_SESSION["rolActivo"] === "soporte") {
    header("Location: ../../public/soporte.php");
    exit;
} elseif($_SESSION["rolActivo"] === "solicitante") {
    header("Location: ../../public/solicitante.php");
    exit;

}

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