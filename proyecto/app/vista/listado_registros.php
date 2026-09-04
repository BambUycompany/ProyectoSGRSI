<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Registros</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/registroSalaCSS.css">
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
        <section class="seccionListadoRegistros">
            <h2>Visualizar Registros de uso de laboratorios</h2>
            <div class="filtrarFecha">
                <button type="button">Filtrar por fecha de expedición</button>
            </div>
             <?php if (count($planillas) === 0): ?>
                <p>Todavía no hay registros cargados.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Aula</th>
                            <th>Hora de expedicion</th>

                            <th>Solicitante</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php foreach ($planillas as $planilla): ?>
                                <tr>
                                    <td>
                                        <a href="detalle_registros.php?id=<?= (int) $planilla['ID'] ?>"> 
                                            <?= htmlspecialchars($planilla['Fecha']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars(ucfirst($planilla['AulaTipo'])) ?> <?= htmlspecialchars($planilla['AulaNumero']) ?></td>
                                    <td><?= htmlspecialchars($planilla['HoraSalida'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($planilla['NombreSolicitante'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>    
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
    <script src="../js/navbar_responsive.js"></script>

    
</body>
</html>