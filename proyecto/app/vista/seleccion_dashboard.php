<?php
$opciones = [
    "administrador" => ["label" => "Panel Administrador", "destino" => "../../public/administrador.php"],
    "soporte"       => ["label" => "Panel Soporte",       "destino" => "../../public/soporte.php"],
    "solicitante"   => ["label" => "Panel Solicitante",   "destino" => "../../public/solicitante.php"],
];
?>
<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccion de rol</title>
</head>
<body>
    <main>
         <h1>¿Con qué rol querés ingresar?</h1>
            <div class="seleccion_dashboard">
            <?php foreach ($_SESSION["roles"] as $rol): ?>
                <a href="../app/controlador/fijarRol.php?rol=<?= urlencode($rol) ?>" class="btn-rol">
                    <?= $opciones[$rol]["label"] ?>
                </a>
            <?php endforeach; ?>
        </div>
        </section>
</body>
</html>