<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Aula</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/detalleAulaCSS.css">
</head>
<body>
    <header>
        <nav>
            <button class="btnMenu" id="btnMenu" type="button"><i class="bi bi-list"></i></button>
            <button class="btnCerrarMenu" id="btnCerrarMenu" type="button"><i class="bi bi-list"></i></button>
            <h1><a href="administrador.php"><img src="assets/img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo" class="logo"> S.G.R.S.I</a></h1>
            <ul class="listaNavegacion">
                <li data-roles="solicitante administrador soporte"><a href="registro_planilla.php" class="botones">Registro Sala</a></li>
                <li data-roles="administrador"><a href="gestor_recursos.php" class="botones">Gestor de Recursos</a></li>
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
        <a href="gestor_recursos.php">&larr; Volver al Gestor de Recursos</a>

        <?php if (isset($_GET["error"])): ?>
            <p style="color:red;"><?= htmlspecialchars($_GET["error"]) ?></p>
        <?php endif; ?>
        <?php if (isset($_GET["resultado"])): ?>
            <p style="color:green;"><?= htmlspecialchars($_GET["resultado"]) ?></p>
        <?php endif; ?>

        <section class="seccionDetalleAula">
            <div class="cabeceraTabla">
                <h2><?= htmlspecialchars(ucfirst($aula['Tipo'])) ?> <?= htmlspecialchars($aula['Numero']) ?></h2>
                <button type="button" class="btnOperacion" id="btnAgregarEquipo">Agregar Equipo</button>
            </div>

            <?php if (count($equipos) === 0): ?>
                <p>Todavía no hay equipos cargados en esta aula.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>PC</th>
                            <th>Modelo PC</th>
                            <th>Monitor</th>
                            <th>Mouse</th>
                            <th>Teclado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipos as $equipo): ?>
                            <tr>
                                <td><?= htmlspecialchars($equipo['NumPc']) ?></td>
                                <td><?= htmlspecialchars($equipo['Modelo'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($equipo['Monitor'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($equipo['Mouse']) ?></td>
                                <td><?= htmlspecialchars($equipo['Teclado']) ?></td>
                                <td>
                                    <div class="cajaOperaciones">
                                        <button type="button"
                                                class="btnOperacion btnModificarEquipo"
                                                data-numpc="<?= htmlspecialchars($equipo['NumPc']) ?>"
                                                data-modelopc="<?= htmlspecialchars($equipo['Modelo'] ?? '') ?>"
                                                data-monitor="<?= htmlspecialchars($equipo['Monitor'] ?? '') ?>"
                                                data-mouse="<?= htmlspecialchars($equipo['Mouse']) ?>"
                                                data-teclado="<?= htmlspecialchars($equipo['Teclado']) ?>">
                                            Modificar
                                        </button>

                                        <form action="../app/controlador/procesarBajaEquipo.php" method="post" class="formularioEliminarEquipo" onsubmit="return confirm('¿Está seguro de eliminar este equipo?');">
                                            <input type="hidden" name="numPc" value="<?= htmlspecialchars($equipo['NumPc']) ?>">
                                            <input type="hidden" name="aulaId" value="<?= (int) $aula['ID'] ?>">
                                            <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION["csrfToken"] ?? '') ?>">
                                            <button type="submit" class="btnOperacion btnEliminar">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <dialog class="dialogAgregarEquipo" id="dialogAgregarEquipo">
            <button type="button" class="btnCerrarModal" id="btnCerrarAgregarEquipo">&times;</button>
            <form action="../app/controlador/procesarAltaEquipo.php" method="post" id="formAgregarEquipo">
                <h2>Agregar Equipo</h2>
                <input type="hidden" name="aulaId" value="<?= (int) $aula['ID'] ?>">

                <label for="cantidad">Cantidad de PCs iguales:</label>
                <input type="number" id="cantidad" name="cantidad" min="1" value="1" required>

                <label for="modeloPc">Modelo de PC:</label>
                <input type="text" id="modeloPc" name="modeloPc" maxlength="150" placeholder="Ej: Mini Pc Dell Optiplex 3020m I3 4160" required>

                <label for="monitor">Monitor (marca):</label>
                <input type="text" id="monitor" name="monitor" maxlength="100" placeholder="Ej: Dell" required>

                <label for="modeloMouse">Modelo de Mouse:</label>
                <input type="text" id="modeloMouse" name="modeloMouse" maxlength="100" required>

                <label for="modeloTeclado">Modelo de Teclado:</label>
                <input type="text" id="modeloTeclado" name="modeloTeclado" maxlength="100" required>

                <button type="submit">Agregar</button>
            </form>
        </dialog>

        <dialog class="dialogModificarEquipo" id="dialogModificarEquipo">
            <button type="button" class="btnCerrarModal" id="btnCerrarModificarEquipo">&times;</button>
            <form action="../app/controlador/procesarModificarEquipo.php" method="post" id="formModificarEquipo">
                <h2>Modificar Equipo</h2>
                <input type="hidden" name="numPc" id="modificarNumPc">
                <input type="hidden" name="aulaId" value="<?= (int) $aula['ID'] ?>">

                <label for="modificarModeloPc">Modelo de PC:</label>
                <input type="text" id="modificarModeloPc" name="modeloPc" maxlength="150" required>

                <label for="modificarMonitor">Monitor (marca):</label>
                <input type="text" id="modificarMonitor" name="monitor" maxlength="100" required>

                <label for="modificarModeloMouse">Modelo de Mouse:</label>
                <input type="text" id="modificarModeloMouse" name="modeloMouse" maxlength="100" required>

                <label for="modificarModeloTeclado">Modelo de Teclado:</label>
                <input type="text" id="modificarModeloTeclado" name="modeloTeclado" maxlength="100" required>

                <button type="submit">Guardar Cambios</button>
            </form>
        </dialog>
    </main>

    <script src="assets/js/navbar_responsive.js"></script>
    <script src="assets/js/detalle_aula.js"></script>