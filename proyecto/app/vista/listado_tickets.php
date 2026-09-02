<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Tickets</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/complete.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/listadoTicketsCSS.css">
</head>
<body>
    <header class="barraNav">
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
                        <li><button type="button" id="btnCerrarSesion">Cerrar sesión</button></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="seccionListadoTickets">
            <h2>Listado de Tickets</h2>
            <?php if (count($ticketsAgrupados) === 0): ?>
                <p>No hay tickets registrados.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>PC</th>
                            <th>Sala</th>
                        <th>Cantidad de reportes</th>
                    </tr>
                </thead>
                <tbody class="listadoTablaTickets">
                    <?php foreach ($ticketsAgrupados as $grupo): ?>
                        <tr>
                            <td><?= htmlspecialchars($grupo['PcNumPc']) ?></td>
                            <td><?= htmlspecialchars($grupo['AulaTipo']) ?> <?= htmlspecialchars($grupo['AulaNumero']) ?></td>
                            <td><?= $grupo['CantidadReportes'] ?></td>
                            <td><a href="detalle_tickets.php?pc=<?= urlencode($grupo['PcNumPc']) ?>&aulaId=<?= (int) $grupo['PcAulaID'] ?>" class="botones">Ver Detalle</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </section>
    </main>
    <script src="../assets/js/listado_tickets.js"></script>
<script src="../assets/js/seleccion_rol.js"></script>
<script src="../assets/js/navbarResponsive.js"></script>
</body>
</html>