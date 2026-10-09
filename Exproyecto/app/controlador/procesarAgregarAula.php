<?php
require_once __DIR__ . "/../../config/config.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Método no permitido."));
    exit;
}

$tipo = trim($_POST["tipo"] ?? "");
$numero = trim($_POST["numero"] ?? "");

if ($tipo === "" || $numero === "") {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Faltan datos del aula."));
    exit;
}

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosAula.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

$accesoDatosAula = new AccesoDatosAula($conexion);

if ($accesoDatosAula->existeAula($tipo, $numero)) {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("El " . $tipo . " número " . $numero . " ya existe."));
    exit();
}


$accesoDatosAula->crearAula($tipo, $numero);

header("Location: ../../public/gestor_recursos.php?resultado=" . urlencode("Aula agregada correctamente."));
exit;
?>