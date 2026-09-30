<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del ticket</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/detalleTickets.css">
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
        <a href="listado_tickets.php">&larr; Volver</a> 

        <section class="seccionDetalleTickets">
            <h2>Detalle del ticket</h2>

            <?php if (count($tickets) === 0): ?>
                <p>No se encontraron tickets para la PC <?= htmlspecialchars($numPc) ?>;</p>
            <?php else: ?>
                <table>
                    <legend>Tickets para la <?= htmlspecialchars($numPc) ?> en la sala <?= htmlspecialchars($tickets[0]['AulaTipo']) ?> <?= htmlspecialchars($tickets[0]['AulaNumero']) ?></legend>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Falla</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Hora de reporte</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?= htmlspecialchars($ticket['ID']) ?></td>
                                <td><?= htmlspecialchars($ticket['Fallo']) ?></td>
                                <td><?= htmlspecialchars($ticket['Descripcion']) ?></td>
                                <td><?= htmlspecialchars($ticket['Estado']) ?></td>
                                <td><?= htmlspecialchars($ticket['Prioridad'])?></td>
                                <td><?= htmlspecialchars($ticket['FechaCreacion']) ?></td>
                                <td>
                                    <?php if ($ticket['Estado'] !== 'finalizado'): ?>
                                        <div class="cajaOperaciones">
                                            <button type="button" 
                                                    class="btnOperacion btnPrioridad" 
                                                    data-id="<?= $ticket['ID'] ?>" 
                                                    data-prioridad="<?= htmlspecialchars($ticket['Prioridad']) ?>">
                                                Prioridad
                                            </button>
                                            <button type="button" 
                                                    class="btnOperacion btnEstado" 
                                                    data-id="<?= $ticket['ID'] ?>" 
                                                    data-estado="<?= htmlspecialchars($ticket['Estado']) ?>">
                                                Estado
                                            </button>
                                            <button type="button" 
                                                    class="btnOperacion btnFinalizar" 
                                                    data-id="<?= $ticket['ID'] ?>">
                                                Finalizar Ticket
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span>Ticket Finalizado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>   
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <!-- Dialog Prioridad -->
    <dialog id="dlgPrioridad">
        <form method="POST" action="../app/controlador/procesarModificarDetalleTicket.php">
            <h3>Cambiar Prioridad</h3>
            <input type="hidden" name="accion" value="cambiar_prioridad">
            <input type="hidden" name="ticket_id" id="prioridad_ticket_id">
            <input type="hidden" name="pc" value="<?= htmlspecialchars($numPc) ?>">
            <input type="hidden" name="aulaId" value="<?= htmlspecialchars($aulaId) ?>">
            
            <label for="selectPrioridad">Prioridad:</label>
            <select name="prioridad" id="selectPrioridad" required>
                <option value="baja">baja</option>
                <option value="media">media</option>
                <option value="alta">alta</option>
            </select>

            <menu>
                <button type="button" class="btnCancelarModal">Cancelar</button>
                <button type="submit">Guardar</button>
            </menu>
        </form>
    </dialog>

    <!-- Dialog Estado -->
    <dialog id="dlgEstado">
        <form method="POST" action="../app/controlador/procesarModificarDetalleTicket.php">
            <h3>Cambiar Estado</h3>
            <input type="hidden" name="accion" value="cambiar_estado">
            <input type="hidden" name="ticket_id" id="estado_ticket_id">
            <input type="hidden" name="pc" value="<?= htmlspecialchars($numPc) ?>">
            <input type="hidden" name="aulaId" value="<?= htmlspecialchars($aulaId) ?>">
            
            <label for="selectEstado">Estado:</label>
            <select name="estado" id="selectEstado" required>
                <option value="pendiente">pendiente</option>
                <option value="en proceso">en proceso</option>
                <option value="finalizado">finalizado</option>
            </select>

            <menu>
                <button type="button" class="btnCancelarModal">Cancelar</button>
                <button type="submit">Guardar</button>
            </menu>
        </form>
    </dialog>

    <!-- Dialog Finalizar Ticket -->
    <dialog id="dlgFinalizar">
        <form method="POST" action="../app/controlador/procesarModificarDetalleTicket.php">
            <h3>Finalizar Ticket</h3>
            <input type="hidden" name="accion" value="finalizar_ticket">
            <input type="hidden" name="ticket_id" id="finalizar_ticket_id">
            <input type="hidden" name="pc" value="<?= htmlspecialchars($numPc) ?>">
            <input type="hidden" name="aulaId" value="<?= htmlspecialchars($aulaId) ?>">
            
            <label for="txtDiagnostico">Diagnóstico / Observaciones:</label><br>
            <textarea name="diagnostico" id="txtDiagnostico" rows="4" required placeholder="Escriba el diagnóstico del problema..."></textarea>

            <menu>
                <button type="button" class="btnCancelarModal">Cancelar</button>
                <button type="submit">Finalizar Ticket</button>
            </menu>
        </form>
    </dialog>

    <script src="assets/js/detalle_tickets.js"></script>
</body>
</html>