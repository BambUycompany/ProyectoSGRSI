<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.G.R.S.I</title>
    <link rel="stylesheet" href="../css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/complete.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/indexCSS.css">
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

             <h1><a href="index.html"><img src="../html/assets\img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo " class="logo"> S.G.R.S.I </a></h1>
            <ul class="listaNavegacion">
               
                <li><a href="registro_sala.html" class="botones">Registro Sala</a></li>
                <li><a href="prestamos.html" class="botones">Prestamos</a></li>
                <li><a href="visualizar_solicitudes.html" class="botones">Solicitudes</a></li>
                <li><a href="login.html" class="botones"><i class="bi bi-person-fill"></i></a></li>
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
            <div style="margin-top:1rem;">
                <button type="button" class="botones">Agregar usuario</button>
            </div>
        </section>

        <section id="soporte" class="role-section" style="display:none;">
            <h2>Vista soporte técnico</h2>
            <section class="seccionInteractiva">
                <a href="registro_sala.html" class="botones">Registro Sala</a>
                <a href="prestamos.html" class="botones">Solicitar Préstamo</a>
                <a href="visualizar_solicitudes.html" class="botones">Visualizar Solicitudes</a>
                <a href="metricas.html" class="botones">Métricas</a>
            </section>
            
        </section>
    </main>

    

    <script src="../js/seleccion_rol.js"></script>
    <script src="../js/navbar_responsive.js"></script>

</body>
</html>