<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.G.R.S.I</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/complete.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/indexCSS.css">
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
               
                <li data-roles="solicitante administrador soporte"><a href="registro_sala.html" class="botones">Registro Sala</a></li>
                <li data-roles="solicitante administrador"><a href="prestamos.html" class="botones">Prestamos</a></li>
                <li data-roles="solicitante administrador"><a href="visualizar_solicitudes.html" class="botones">Solicitudes</a></li>
                <li data-roles="administrador soporte"><a href="metricas.html" class="botones">Métricas</a></li>
                <li data-roles="soporte"><a href="listado_tickets.html" class="botones">Tickets</a></li>

                <li id="navAgregarUsuario" data-roles="administrador">
                    <a href="registro_empleados.html" class="botones">Agregar usuario</a>
                </li>
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
        <section id="solicitante" class="role-section" style="display:none;">
            <h2>Vista solicitante</h2>
            <section class="seccionInteractiva">
                <a href="registro_sala.html" class="botones">Registro Sala</a>
                <a href="prestamos.html" class="botones">Solicitar Préstamo</a>
                <a href="visualizar_solicitudes.html" class="botones">Visualizar Solicitudes</a>
            </section>
        </section>

        <section id="administrador" class="role-section" style="display:none;">
            <h2>Vista administrador</h2>
            <section class="seccionInteractiva">
                <a href="registro_sala.html" class="botones">Registro Sala</a>
                <a href="prestamos.html" class="botones">Solicitar Préstamo</a>
                <a href="visualizar_solicitudes.html" class="botones">Visualizar Solicitudes</a>
                <a href="metricas.html" class="botones">Métricas</a>
            </section>

            <div class="seccionAgregarUsuario">
                <a href="registro_empleados.html" class="botones">Agregar usuario</a>
            </div>
            <div class="seccionAgregarAula">
                <a href="registro_aulas.html" class="botones">Agregar aula</a>
            </div>
        </section>

        <section id="soporte" class="role-section" style="display:none;">
            <h2>Vista soporte técnico</h2>
            <section class="seccionInteractiva">
                <a href="registro_sala.html" class="botones">Registro Sala</a>
                <a href="metricas.html" class="botones">Métricas</a>
                <a href="listado_tickets.html" class="botones">Tickets</a>
            </section>
            </section>
            
        </section>
    </main>


    <script src="../assets/js/seleccion_rol.js"></script>
    <script src="../assets/js/navbar_responsive.js"></script>
    

</body>
</html>