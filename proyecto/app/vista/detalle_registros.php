<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Registro</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
        </ul>
    </nav>
</header>

    <main>
        <a href="<?= $_SESSION["rolActivo"] === "solicitante" ? "mis_registros.php" : "listado_registro.php" ?>">&larr; Volver al listado</a>
        <section class="seccionDetalleRegistro">
            <h2>Detalle del Registro</h2>

            <p>Fecha: <?= htmlspecialchars($planilla['Fecha']) ?></p>
            <p>Hora: <?= htmlspecialchars($planilla['HoraEntrada']) ?> - <?= htmlspecialchars($planilla['HoraSalida'] ?? '-') ?></p>
            <p>Sala: <?= htmlspecialchars(ucfirst($planilla['AulaTipo'])) ?> <?= htmlspecialchars($planilla['AulaNumero']) ?></p>
            <p>Solicitante: <?= htmlspecialchars($planilla['NombreSolicitante'] ?? '-') ?></p>

            <h3>Fallas reportadas</h3>

            <?php if (count($tickets) === 0): ?>
                <p>No se reportó ninguna falla en este registro.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>PC</th>
                            <th>Falla</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?= htmlspecialchars($ticket['PcNumPc']) ?></td>
                                <td><?= htmlspecialchars($ticket['Fallo']) ?></td>
                                <td><?= htmlspecialchars($ticket['Descripcion']) ?></td>
                                <td><?= htmlspecialchars($ticket['Estado']) ?></td>
                                <td><?= htmlspecialchars(date('H:i', strtotime($ticket['FechaCreacion']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <script src="assets/js/navbar_responsive.js"></script>
</body>
</html>