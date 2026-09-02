<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosAula.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosAula = new AccesoDatosAula($conexion);
$aulas = $accesoDatosAula->listarAulasConDetalle();
?>