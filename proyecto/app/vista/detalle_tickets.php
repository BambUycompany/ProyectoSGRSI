<!DOCTYPE html>
<html lang="en, es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del ticket</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/detalleTickets.css">
</head>
<body>
    <main>
        <a href="listado_tickets.php">&larr; Volver</a> 

        <section class="seccionDetalleTickets">
            <h2>Detalle del ticket</h2>

            <?php if (count($tickets) === 0): ?>
                <p>No se encontraron tickets para la PC <?= htmlspecialchars($numPc) ?>;
            <?php else: ?>
                <table>
                    <legend>Tickets para la <?= htmlspecialchars($numPc) ?> en la sala <?= htmlspecialchars($tickets[0]['AulaTipo']) ?> <?= htmlspecialchars($tickets[0]['AulaNumero']) ?></legend>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Falla</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Hora de reporte</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?= htmlspecialchars($ticket['ID']) ?></td>
                                <td><?= htmlspecialchars($ticket['Fallo']) ?></td>
                                <td><?= htmlspecialchars($ticket['Descripcion']) ?></td>
                                <td><?= htmlspecialchars($ticket['Estado']) ?></td>
                                <td><?= htmlspecialchars($ticket['FechaCreacion']) ?></td>
                            </tr>
                        <?php endforeach; ?>   
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>