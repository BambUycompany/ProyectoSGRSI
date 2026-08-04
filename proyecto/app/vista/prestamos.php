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

             <h1><a href="index.php"><img src="../assets\img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo " class="logo"> S.G.R.S.I </a></h1>
            <ul class="listaNavegacion">
               
                <li><a href="registro_sala.php" class="botones">Registro Sala</a></li>
                <li><a href="prestamos.php" class="botones">Prestamos</a></li>
                <li><a href="visualizar_solicitudes.php" class="botones">Solicitudes</a></li>
                <li><a href="login.php" class="botones"><i class="bi bi-person-fill"></i></a></li>
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
                    <input type="text" id="Clase" name="Clase" maxlength="10" required>

                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="Email" maxlength="100" required>

                    <label for="Cedula">Cédula:</label>
                    <input type="text" id="Cedula" name="Cedula" pattern="[0-9]{8}" maxlength="8" inputmode="numeric" required>

                    <label for="Telefono">Teléfono:</label>
                    <input type="text" id="Telefono" name="Telefono" pattern="[0-9]{8,9}" maxlength="9" inputmode="numeric" required>

                    <input type="submit" class="botones" value="Solicitar">
                </form>
            </section>
        </section>
    </main>
    <script src="../assets/js/navbar_responsive.js"></script>


</body>
</html>