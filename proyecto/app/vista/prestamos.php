<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestamos</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/prestamosCSS.css">
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
        <section class="seccionPrestamos">
            <h2>Gestion de Prestamos</h2>
            <section class="formPrestamos"> 
                <form action="index.php" method="post">
                    <label for="NombreAlumno">Nombre del Alumno:</label>
                    <input type="text" id="NombreAlumno" name="NombreAlumno" required>

                    <label for="Clase">Clase:</label>
                    <input type="text" id="Clase" name="Clase" required>

                    <label for="Email">Email:</label>
                    <input type="text" id="Email" name="Email" required>

                    <label for="Cedula">Cédula:</label>
                    <input type="text" id="Cedula" name="Cedula" required>

                    <label for="Telefono">Teléfono:</label>
                    <input type="text" id="Telefono" name="Telefono" required>

                    <input type="submit" class="botones" value="Solicitar">
                </form>
            </section>
        </section>
    </main>
    <script src="../assets/js/navbar_responsive.js"></script>


</body>
</html>