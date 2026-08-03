<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Sala informatica</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/registroSalaCSS.css">
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

             <h1><a href="index.html"><img src="../assets\img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo " class="logo"> S.G.R.S.I </a></h1>
            <ul class="listaNavegacion">
               
                <li><a href="registro_sala.html" class="botones">Registro Sala</a></li>
                <li><a href="prestamos.html" class="botones">Prestamos</a></li>
                <li><a href="visualizar_solicitudes.html" class="botones">Solicitudes</a></li>
                <li><a href="login.html" class="botones"><i class="bi bi-person-fill"></i></a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="seccionRegistroSala">
            <h2>Registro de uso Sala informatica</h2>
            <section class="formSala">
                <form action="index.html" method="post">

                
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

                    <label for="Asignatura">Asignatura:</label>
                    <input type="text" id="Asignatura" name="Asignatura" required>

                    <label for="Docente">Docente:</label>
                    <input type="text" id="Docente" name="Docente" required>
        
                    <label for="grupo">Grupo:</label>
                    <input type="text" id="grupo" name="grupo" required>

                    <label for="turno">Turno:</label>
                    <select id="turno" name="turno" required>
                        <option value="">Seleccionar</option>
                        <option value="mañana">Mañana</option>
                        <option value="tarde">Tarde</option>
                        <option value="noche">Noche</option>
                    </select>

                    <input type="submit" value="Registrar" class="btnRegistrar">  
            </form>
            
        </section>
        <button type="button" class="btnOperacion" id="btnCrearTicket">Reportar falla técnica</button>
    </main>
    <section class="creacionTicket" id="creacionTicket">
        <button type="button" class="btnCerrarModal" onclick="cerrarModal()">x</button>
        <form action="registro_sala.html" method="post">
            <h2>Creacion de ticket </h2>
            <label for="numeroPc">Número de PC:</label><br>
            <input type="text" id="numeroPc" name="numeroPc" required><br><br>
            <label for="nombreEstudiante">Nombre completo del estudiante:</label><br>
            <input type="text" id="nombreEstudiante" name="nombreEstudiante" required><br><br>
            <label for="fallo" >Tipo de falla:</label><br>
            <select id="fallo" name="fallo" required>
                <option value="">Seleccionar</option>
                <option value="hardware">Falta mouse</option>
                <option value="software">Falta teclado</option>
                <option value="red">No prende</option>
                <option value="otro">No tiene almacenamiento</option>
                <option value="otro">Otro</option>
            </select><br><br>

            </label>
            <label for="descripcion">Descripción de la falla:</label><br>
            <textarea id="descripcion" name="descripcion" rows="4" cols="50" required></textarea><br><br>

            <input type="submit" value="Enviar Reporte">
        </form>
    </section>
    <script src="../assets/js/creacion_ticket.js"></script>
    <script src="../assets/js/actualizar_numero.js"> </script>
    <script src="../assets/js/navbar_responsive.js"></script>

    
</body>
</html>