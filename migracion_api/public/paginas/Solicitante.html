<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.G.R.S.I</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/complete.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/indexCSS.css">
</head>
<body>
    <header class="barraNav">
    <nav>
        <button class="btnMenu" id="btnMenu" type="button"><i class="bi bi-list"></i></button>
        <button class="btnCerrarMenu" id="btnCerrarMenu" type="button"><i class="bi bi-list"></i></button>

        <h1><a href="<?= homeSegunRol() ?>"><img src="assets/img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo" class="logo"> S.G.R.S.I</a></h1>
        <ul class="listaNavegacion">
            <li data-roles="administrador solicitante soporte"><a href="registro_planilla.php" class="botones">Registro Sala</a></li>
            <li data-roles="administrador"><a href="registro_empleados.php" class="botones">Empleados</a></li>
            <li data-roles="administrador soporte"><a href="gestor_recursos.php" class="botones">Gestor de Recursos</a></li>
            <li data-roles="soporte"><a href="listado_registro.php" class="botones">Listado Registro</a></li>
            <li data-roles="soporte"><a href="listado_tickets.php" class="botones">Tickets</a></li>
            <li data-roles="administrador soporte"><a href="metricas.php" class="botones">Métricas</a></li>
            <li data-roles="solicitante"><a href="registro_prestamo.php" class="botones">Solicitar Préstamo</a></li>
            <li data-roles="solicitante"><a href="listado_prestamos.php" class="botones">Mis Préstamos</a></li>
            <li class="menuUsuario">
                <button type="button" id="btnIconoUsuario" class="botones"><i class="bi bi-person-fill"></i></button>
                <ul class="opcionesUsuario" id="opcionesUsuario">
                    <?php if (isset($_SESSION["roles"]) && count($_SESSION["roles"]) > 1): ?>
                        <li><button type="button" id="btnCambiarRol">Cambiar de rol</button></li>
                    <?php endif; ?>
                    <li><button type="button" id="btnCerrarSesion">Cerrar sesión</button></li>
                </ul>
            </li>

             <li data-roles="solicitante" class="menuNotificaciones">
                <button type="button" id="btnCampanaNotif" class="botones position-relative">
                    <i class="bi bi-bell-fill"></i>
                    <?php if (!empty($notificacionesFinalizadas)): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= count($notificacionesFinalizadas) ?>
                        </span>
                    <?php endif; ?>
                </button>
            </li>
        </ul>
    </nav>
</header>
    

    <main>
        <section id="solicitante"> 
            <h2>Bienvenido Solicitante, <?= htmlspecialchars($_SESSION["nombre"] . " " . $_SESSION["apellido"]) ?></h2>            
            <section class="seccionInteractiva">
                <a href="registro_planilla.php" class="botones">Registro Planilla</a>
                <a href="solicitar_prestamos.php" class="botones">Solicitar Préstamo</a>
                <a href="visualizar_registros.php" class="botones">Visualizar Registros</a>
                <a href="mailto:@ezequielobedrodriguez@gmail.com" class="botones">Solicitud de servicio</a>
                <legend> <i>Para gestionar una solicitud de servicio se pide al personal enviar un mail con el asunto <strong>"SOLICITUD DE SERVICIO - AULA X"</strong></i></legend><br><br>
            </section>

             <?php if (isset($_GET["error"])): ?>
            <p style="color:red;"><?= htmlspecialchars($_GET["error"]) ?></p>
        <?php endif; ?>

        <section class="seccionListadoRegistro">
            <h2>Mis Registros</h2>

            <section class="filtroPeriodo">
                <a href="Solicitante.php?periodo=dia" class="botones <?= $periodo === 'dia' ? 'activo' : '' ?>">Hoy</a>
                <a href="Solicitante.php?periodo=semana" class="botones <?= $periodo === 'semana' ? 'activo' : '' ?>">Esta semana</a>
                <a href="Solicitante.php?periodo=mes" class="botones <?= $periodo === 'mes' ? 'activo' : '' ?>">Este mes</a>
                <a href="Solicitante.php?periodo=todo" class="botones <?= $periodo === 'todo' ? 'activo' : '' ?>">Todo</a>
            </section>

            <?php if (count($misPlanillas) === 0): ?>
                <p>No tenés registros en este período.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Sala</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($misPlanillas as $planilla): ?>
                            <tr>
                                <td>
                                    <a href="detalle_registros.php?id=<?= (int) $planilla['ID'] ?>">
                                        <?= htmlspecialchars($planilla['Fecha']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($planilla['HoraEntrada']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($planilla['AulaTipo'])) ?> <?= htmlspecialchars($planilla['AulaNumero']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
        </section>
    </main>

       

    <script src="../public/assets/js/navbar_responsive.js"></script>
    <script src="../public/assets/js/notificaciones.js"></script>


</body>
</html>