
<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Sala informatica</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/registroPlanillaCSS.css">
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
               
                <li data-roles="solicitante administrador soporte"><a href="registro_sala.html" class="botones">Registro Sala</a></li>
                <li data-roles="administrador soporte"><a href="metricas.html" class="botones">Métricas</a></li>
                <li data-roles="soporte"><a href="listado_tickets.html" class="botones">Tickets</a></li>
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
        <?php
            if (isset($_GET["error"])) {
                echo "<p style='color:red;'>" . htmlspecialchars($_GET["error"]) . "</p>";
            }
            if (isset($_GET["resultado"])) {
                echo "<p style='color:green;'>" . htmlspecialchars($_GET["resultado"]) . "</p>";
            }
        ?>
        <section class="seccionRegistroSala">
            <h2>Registro de uso Sala informatica</h2>
            <section class="formSala">
                <form action="../app/controlador/procesarRegistroPlanilla.php" method="POST">

                
                    <label for="tipo">Tipo de sala:</label>
                    <select id="tipo" name="tipo" required onchange="actualizarNumeros()">
                        <option value="">Seleccionar</option>
                        <option value="laboratorio">Laboratorio</option>
                        <option value="taller">Taller</option>
                    </select>
                    <label for="numero">Número:</label>
                    <select id="numero" name="numero" required>
                        <option value="">Seleccionar</option>
                    </select>
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" required>

                    <label for="horaEntrada">Hora Entrada:</label>
                    <input type="time" id="horaEntrada" name="horaEntrada" required>

                    <label for="horaSalida">Hora Salida:</label>
                    <input type="time" id="horaSalida" name="horaSalida" required> 

                    <label for="nombreSolicitante">Solicitante:</label>
                    <input type="text" id="nombreSolicitante" name="nombreSolicitante" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" maxlength="50" required>

                 <?php if ($_SESSION["rolActivo"] !== "soporte"): ?>
                    <label for="Asignatura">Asignatura:</label>
                    <input type="text" id="Asignatura" name="Asignatura" maxlength="35" required>

                    <label for="grupo">Grupo:</label>
                    <input type="text" id="grupo" name="grupo" maxlength="10" required>

                    <label for="turno">Turno:</label>
                    <select id="turno" name="turno" required>
                        <option value="">Seleccionar</option>
                        <option value="mañana">Mañana</option>
                        <option value="tarde">Tarde</option>
                        <option value="noche">Noche</option>
                    </select>
                <?php endif; ?>
                    <div id="hiddenInputsTickets"></div>

                    <input type="submit" value="Registrar" class="btnRegistrar">
                </form>
            </section>

            <div id="resumenTickets"></div>

            <button type="button" class="btnOperacion" id="btnCrearTicket">Reportar falla técnica</button>
        </section>
    </main>

    <section class="creacionTicket" id="creacionTicket">
        <button type="button" class="btnCerrarModal" id="btnCerrarModalTicket">x</button>
        <h2>Creación de ticket</h2>

        <label for="numeroPc">Número de PC:</label><br>
        <input type="text" id="numeroPc" pattern="PC-[0-9]{2}" maxlength="5" placeholder="Ej: PC-01" required><br><br>

        <label for="fallo">Tipo de falla:</label><br>
        <select id="fallo" required>
            <option value="">Seleccionar</option>
            <option value="falta_mouse">Falta mouse</option>
            <option value="falta_teclado">Falta teclado</option>
            <option value="no_prende">No prende</option>
            <option value="sin_almacenamiento">No tiene almacenamiento</option>
            <option value="otro">Otro</option>
        </select><br><br>

        <label for="descripcion">Descripción de la falla:</label><br>
        <textarea id="descripcion" rows="4" cols="50" required></textarea><br><br>

        <button type="button" id="btnAgregarTicketALaLista">Agregar a la lista</button>
    </section>

   
    <script src="assets/js/registro_planilla.js"></script>

    <script src="assets/js/actualizar_numero.js"> </script>
    <script src="assets/js/navbar_responsive.js"></script>

    
</body>
</html>