<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Préstamo</title>
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

        <section class="seccionRegistroPrestamo">
            <h2>Solicitar Préstamo de Portátil</h2>

            <?php if (count($portatilesDisponibles) === 0): ?>
                <p>No hay portátiles disponibles en este momento.</p>
            <?php else: ?>
                <form action="../app/controlador/procesarRegistroPrestamo.php" method="POST">
                    <label for="portatilId">Portátil:</label>
                    <select id="portatilId" name="portatilId" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($portatilesDisponibles as $portatil): ?>
                            <option value="<?= (int) $portatil['ID'] ?>"><?= htmlspecialchars($portatil['Modelo']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="ciAlumno">Cédula del alumno:</label>
                    <input type="text" id="ciAlumno" name="ciAlumno" pattern="[0-9]{8}" maxlength="8" required>

                    <label for="clase">Clase / Grupo:</label>
                    <input type="text" id="clase" name="clase" maxlength="50" required>

                    <label for="correoAlumno">Correo del alumno (opcional):</label>
                    <input type="email" id="correoAlumno" name="correoAlumno" maxlength="150">

                    <label for="telefonoAlumno">Teléfono del alumno (opcional):</label>
                    <input type="text" id="telefonoAlumno" name="telefonoAlumno" maxlength="20">

                    <label for="fechaDev">Fecha comprometida de devolución:</label>
                    <input type="date" id="fechaDev" name="fechaDev" required>

                    <button type="submit">Registrar Préstamo</button>
                </form>
            <?php endif; ?>
        </section>
    </main>

    <script src="assets/js/navbar_responsive.js"></script>
</body>
</html>