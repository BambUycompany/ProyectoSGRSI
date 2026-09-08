<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Préstamos</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <button class="btnMenu" id="btnMenu" type="button"><i class="bi bi-list"></i></button>
            <button class="btnCerrarMenu" id="btnCerrarMenu" type="button"><i class="bi bi-list"></i></button>
            <h1><a href="solicitante.php"><img src="assets/img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo" class="logo"> S.G.R.S.I</a></h1>
            <ul class="listaNavegacion">
                <li data-roles="solicitante administrador soporte"><a href="registro_planilla.php" class="botones">Registro Sala</a></li>
                <li data-roles="solicitante"><a href="registro_prestamo.php" class="botones">Solicitar Préstamo</a></li>
                <li data-roles="solicitante"><a href="listado_prestamos.php" class="botones">Mis Préstamos</a></li>
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
        <?php if (isset($_GET["error"])): ?>
            <p style="color:red;"><?= htmlspecialchars($_GET["error"]) ?></p>
        <?php endif; ?>
        <?php if (isset($_GET["resultado"])): ?>
            <p style="color:green;"><?= htmlspecialchars($_GET["resultado"]) ?></p>
        <?php endif; ?>

        <section class="seccionListadoPrestamos">
            <h2>Mis Préstamos</h2>

            <?php if (count($prestamos) === 0): ?>
                <p>Todavía no registraste ningún préstamo.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Portátil</th>
                            <th>Alumno</th>
                            <th>Clase</th>
                            <th>F. Préstamo</th>
                            <th>F. Devolución</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $prestamo): ?>
                            <tr>
                                <td><?= htmlspecialchars($prestamo['PortatilModelo']) ?></td>
                                <td><?= htmlspecialchars($prestamo['CIAlumno']) ?></td>
                                <td><?= htmlspecialchars($prestamo['Clase']) ?></td>
                                <td><?= htmlspecialchars($prestamo['FechaPrestamo']) ?></td>
                                <td><?= htmlspecialchars($prestamo['FechaDev']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($prestamo['Estado'])) ?></td>
                                <td>
                                    <?php if ($prestamo['Estado'] === 'activo'): ?>
                                        <form action="../app/controlador/procesarDevolverPrestamo.php" method="post" onsubmit="return confirm('¿Confirmar devolución de este portátil?');">
                                            <input type="hidden" name="prestamoId" value="<?= (int) $prestamo['ID'] ?>">
                                            <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION["csrfToken"] ?? '') ?>">
                                            <button type="submit" class="btnOperacion">Marcar como devuelto</button>
                                        </form>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
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