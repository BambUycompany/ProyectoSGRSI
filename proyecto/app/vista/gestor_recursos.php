<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de recursos</title>
    link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/gestorRecursosCSS.css">

</head>
<body>
    <header>
        <nav>
            <button class="btnMenu" id="btnMenu" type="button">
                <i class="bi bi-list"></i>
            </button>
            <button class="btnCerrarMenu" id="btnCerrarMenu" type="button">
                <i class="bi bi-list"></i>
            </button>

             <h1><a href="administrador.php"><img src="../public/assets/img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo " class="logo"> S.G.R.S.I </a></h1>
            <ul class="listaNavegacion">
               
                <li data-roles="solicitante administrador soporte"><a href="registro_sala.php" class="botones">Registro Sala</a></li>
                <li data-roles="administrador soporte"><a href="metricas.php" class="botones">Métricas</a></li>
                <li data-roles="soporte"><a href="listado_tickets.php" class="botones">Tickets</a></li>
                <li class="menuUsuario">
                    <button type="button" id="btnIconoUsuario" class="botones"><i class="bi bi-person-fill"></i></button>
                    <ul class="opcionesUsuario" id="opcionesUsuario">
                        <?php if (isset($_SESSION["roles"]) && count($_SESSION["roles"]) > 1): ?>
                            <li><button type="button" id="btnCambiarRol">Cambiar de rol</button></li>
                        <?php endif; ?>
                        <li><button type="button" id="btnCerrarSesion">Cerrar sesión</button></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>
    <main>
        <?php if (isset($_GET["error"])): ?>
            <p style="color:red;"><?= htmlspecialchars($_GET["error"]) ?></p>
        <?php endif; ?>
        <?php if (isset($_GET["resultado"])): ?>
            <p style="color:green;"><?= htmlspecialchars($_GET["resultado"]) ?></p>
        <?php endif; ?>
        <section class="seccionGestorRecursos">
            <h2>Gestor de recursos</h2>
            <div class="opcionesGestor">
                <a href="gestor_aulas.php" class="botones">Gestión de aulas</a>
                <a href="gestor_recursos.php" class="botones">Gestión de recursos</a>
            </div>
        </section>
    
</body>
</html>
