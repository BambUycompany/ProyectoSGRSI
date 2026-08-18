<?php

require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/BajaDatosUsuario.php";

$cedula = trim($_POST["cedula"] ?? "");

if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
    $mensaje = "No se pudo eliminar el empleado: Cédula incorrecta o usuario inexistente.";
    header("Location: registro_empleado.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO("localhost:3306", "root", "", "sgrsi_db");
$conexion = $conectorPDO->establecerConexion();

    if ($conexion === null) {
        $mensaje = "No se pudo establecer conexión con la base de datos.";
        header("Location: registro_empleado.php?error=" . urlencode($mensaje));
        exit;
    }

    $bajaDatosUsuario = new BajaDatosUsuario($conexion);
    $resultado = $bajaDatosUsuario->eliminarUsuario($cedula);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo eliminar el empleado.";
    header("Location: registro_empleado.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Empleado eliminado exitosamente.";
header("Location: registro_empleado.php?resultado=" . urlencode($mensaje));
exit;
?>