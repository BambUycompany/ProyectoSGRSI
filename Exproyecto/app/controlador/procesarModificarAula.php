<?php
require_once __DIR__ . "/../../config/config.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Método no permitido."));
    exit;
}

$aulaId = trim($_POST["aulaId"] ?? "");
$tipo = trim($_POST["tipo"] ?? "");
$numero = trim($_POST["numero"] ?? "");

if ($aulaId === "" || !is_numeric($aulaId) || $tipo === "" || $numero === "") {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Faltan datos requeridos para modificar el aula."));
    exit;
}

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/ModificarDatosAula.php";

$conectorPDO = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("Error de conexión a la base de datos."));
    exit;
}

$modificarDatosAula = new ModificarDatosAula($conexion);
$resultado = $modificarDatosAula->modificarAula((int)$aulaId, $tipo, $numero);

$conectorPDO->desconectar();

if (!$resultado) {
    header("Location: ../../public/gestor_recursos.php?error=" . urlencode("No se pudo actualizar el aula."));
    exit;
}

header("Location: ../../public/gestor_recursos.php?resultado=" . urlencode("Aula modificada exitosamente."));
exit;
?>