<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metricas</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/metricasCSS.css">
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
               
                <li><a href="registro_sala.php" class="botones">Registro Sala</a></li>
                <li><a href="prestamos.php" class="botones">Prestamos</a></li>
                <li><a href="visualizar_solicitudes.php" class="botones">Solicitudes</a></li>
                <li><a href="login.php" class="botones"><i class="bi bi-person-fill"></i></a></li>
            </ul>
        </nav>
    </header>

    <h1>Dashboard de Metricas</h1>

    <main> 
         <div class="filtrarFecha">
                <button type="button" class="botones">Filtrar por Año</button>
                <button type="button" class="botones">Filtrar por mes</button>
                <button type="button" class="botones">Filtrar por semana</button>
            </div>
            <h2>Métricas </h2>
            <section class="seccionMetricas">
    
            <section class="seccionMetrica">
                <h3>Solicitudes realizadas:</h3>
                <p>43</p>   
            </section>
            <section class="seccionMetrica">
                <h3>PCs mas reportadas:</h3>
                <p>PC-03</p>   
            </section>
            <section class="seccionMetrica">
                <h3>Perifericos mas extraviados:</h3>
                <p>Mouse</p>   
            </section>
            <section class="seccionMetrica">
                <h3>PC mas vandalizada:</h3>
                <p>PC-05</p>   
            </section>
            <section class="seccionMetrica">
                <h3>Reportes por rendimiento bajo:</h3>
                <p>67</p>   
            </section>
            </section>
    </main>
        <script src="../assets/js/navbar_responsive.js"></script>

    
</body>
</html>