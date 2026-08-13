<?php

$opciones = [
    "administrador" => ["label" => "Panel Administrador", "destino" => "../../../public/administrador.php"],
    "soporte"       => ["label" => "Panel Soporte",       "destino" => "../../../public/soporte.php"],
    "solicitante"   => ["label" => "Panel Solicitante",   "destino" => "../../../public/solicitante.php"],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de rol</title>
    <link rel="stylesheet" href="/proyecto/public/assets/css/dashboardCSS.css">
    <link rel="stylesheet" href="/proyecto/public/assets/css/global.css">
</head>
<body>
    <main>
        <h1>Seleccione el rol requerido.</h1>
        <div class="seleccion_dashboard">
            <?php 
        
            if (isset($_SESSION["roles"]) && is_array($_SESSION["roles"])): 
                foreach ($_SESSION["roles"] as $rol): 
                    if (isset($opciones[$rol])): ?>
                        <a href="../controlador/fijarRol.php?rol=<?= urlencode($rol) ?>" class="btn-rol">
                            <?= htmlspecialchars($opciones[$rol]["label"]) ?>
                        </a>
                    <?php 
                    endif;
                endforeach; 
            else: 
            ?>
            
                <p style="color: #666; text-align: center; margin: 0;">No hay roles activos en la sesión actual.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
