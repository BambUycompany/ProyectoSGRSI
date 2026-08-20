<?php
session_start();

require_once __DIR__ . "/../app/controlador/control_acceso.php";
requerirRol("soporte");

require_once __DIR__ . "/../app/modelo/ConectorPDO.php";
require_once __DIR__ . "/../app/modelo/AccesoDatosPlanilla.php";

$conectorPDO = new ConectorPDO("localhost:3306", "root", "", "sgrsi_db");
$conexion = $conectorPDO->establecerConexion();

$accesoDatosPlanilla = new AccesoDatosPlanilla($conexion);
$planillas = $accesoDatosPlanilla->listarRegistroPlanilla();

require_once __DIR__ . "/../app/vista/listado_registros.php";
?>